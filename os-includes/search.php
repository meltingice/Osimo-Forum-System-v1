<?php
session_start();
include_once('dbconnect.php'); //connects to database
include_once('paths.php');
include_once('osimo.php');
$osimo = new Osimo(); //makes magic happen

if(isset($_POST['osimo_search_query']))
{
	if(isset($_POST['osimo_search_type'])){ $type = urlencode($_POST['osimo_search_type']); }
	else{ $type = 'content'; }
	
	header("Location: ".OSIMOPATH."search.php?q=".urlencode($_POST['osimo_search_query'])."&type=$type");
}

function _getSearchResults($query,$type,$page,&$numResults,$textColor,$bgColor)
{
	global $osimo;
	if(strlen($query)<3){ return "tooshort"; exit; }
	$query = urldecode($query);
	$query = secureContent($query);
	$searchResults = array();
	$start = ($page-1)*10;
	if($type=='content')
	{
		$search1 = "SELECT thread FROM post WHERE body LIKE '%$query%' ORDER BY post_time DESC";
		$result1 = mysql_query($search1);
		$numResults = mysql_num_rows($result1);
		$i=0;
		$search2 = "SELECT * FROM thread WHERE";
		while($data1 = mysql_fetch_array($result1,MYSQL_ASSOC))
		{
			$search2 .= " id='{$data1['thread']}'";
			if($i > 10){ break; }
			if($i<$numResults-1 ){ $search2 .= " OR"; }
			$i++;
		}
		$search2 .= " OR title LIKE '%$query%' ORDER BY last_post_time DESC LIMIT $start,10";
		$result2 = mysql_query($search2);

		if($result2&&mysql_num_rows($result2)>0)
		{
			$usedThreads = array();
			$numResults = mysql_num_rows($result2);
			while($data2 = mysql_fetch_array($result2))
			{
				if(!in_array($data2['id'],$usedThreads))
				{
					$searchResults[$data2['id']]['forum'] = $data2['forum'];
					$searchResults[$data2['id']]['title'] = $data2['title'];
					$searchResults[$data2['id']]['description'] = $data2['description'];
					$searchResults[$data2['id']]['views'] = $data2['views'];
					$searchResults[$data2['id']]['posts'] = $data2['posts'];
					$searchResults[$data2['id']]['original_poster'] = $data2['original_poster'];
					$searchResults[$data2['id']]['original_poster_id'] = $data2['original_poster_id'];
					$searchResults[$data2['id']]['original_post_time'] = $data2['original_post_time'];
					$searchResults[$data2['id']]['last_poster'] = $data2['last_poster'];
					$searchResults[$data2['id']]['last_poster_id'] = $data2['last_poster_id'];
					$searchResults[$data2['id']]['last_post_time'] = $data2['last_post_time'];
					$searchResults[$data2['id']]['sticky'] = $data2['sticky'];
					$searchResults[$data2['id']]['locked'] = $data2['locked'];
					$usedThreads[] = $data2['id'];
				}
			}
			
			$user = $osimo->getLoggedInUser();
			$osimo->writeToSysLog('search',$user['ID'],$user['name']." performed a content search for: $query");
			return $searchResults;
		}
	}
	elseif($type=='postsby')
	{
		$search = "SELECT id,thread,body,poster_id,poster_username,post_time FROM post WHERE poster_username='$query' ORDER BY post_time DESC LIMIT $start,10";
		$result = mysql_query($search);
		if($result&&mysql_num_rows($result)>0)
		{
			$i=0;
			$numResults = mysql_num_rows($result1);
			include('bbcode/bbparser.php');
			while($data = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				$searchResults[$data['thread']][$i]['id'] = $data['id'];
				$searchResults[$data['thread']][$i]['body'] = bb2html($data['body']);
				$searchResults[$data['thread']][$i]['poster_id'] = $data['poster_id'];
				$searchResults[$data['thread']][$i]['poster_username'] = $data['poster_username'];
				$searchResults[$data['thread']][$i]['post_time'] = $data['post_time'];
				$i++;
			}
			
			$user = $osimo->getLoggedInUser();
			$osimo->writeToSysLog('search',$user['ID'],$user['name']." performed a user post search for $query");
			return $searchResults;
		}
	}

	return "noresults";
}

function _getNumSearchPages($query,$type)
{
	$query = urldecode($query);
	$query = secureContent($query);
	if($type=='content')
	{
		$query = str_replace(' ','%',$query);
		$search1 = "SELECT thread FROM post WHERE body LIKE '%$query%' ORDER BY post_time DESC";
		$result1 = mysql_query($search1);
		$numResults = mysql_num_rows($result1);
		$i=0;
		$search2 = "SELECT COUNT(*) FROM thread WHERE";
		while($data1 = mysql_fetch_array($result1,MYSQL_ASSOC))
		{
		    $search2 .= " id='{$data1['thread']}'";
		    if($i<$numResults-1 ){ $search2 .= " OR"; }
		    $i++;
		}
		$search2 .= " OR title LIKE '%$query%' ORDER BY last_post_time DESC";
		$result2 = mysql_query($search2);
		if($result2&&mysql_num_rows($result2)>0)
		{
			$totalResults = reset(mysql_fetch_row($result2));
		}
		
		return ceil($totalResults/10);
	}
	if($type=='postsby')
	{
		$search = "SELECT COUNT(*) FROM post WHERE poster_username='$query' ORDER BY post_time DESC";
		$result = mysql_query($search);
		if($result&&mysql_num_rows($result)>0)
		{
			$totalResults = reset(mysql_fetch_row($result));
		}
		
		return ceil($totalResults/10);
	}
}

function _getSearchPresetPagination($numPages)
{
	global $osimo;
	if(isset($_GET['type'])){ $type = $_GET['type']; }
	else{ $type = 'contents'; }
	if(isset($_GET['page'])){ $page = $_GET['page']; }
	else{ $page = 1; }
	
	if(isset($_GET['q'])&&$_GET['q']!='')
	{
		$query = $_GET['q'];
	}
	
	$pagination = $osimo->outputPagination($numPages,$page);
	$i=1;
	$numPages = count($pagination);
	foreach($pagination as $pageID)
	{
	    if($i==$numPages&&$pagination[$numPages-1]>5){ echo "... "; }
	    echo "<a href=\"search.php?q=$query&type=$type&page=$pageID\"";
	    if($pageID==$page){ echo " class=\"osimo-active-search-page\""; }
	    echo " id=\"osimo_pagenav-$pageID\">$pageID</a> ";
	    if($i==1&&$pagination[$numPages-1]>5){ echo "... "; }
	    $i++;
	}
}
?>