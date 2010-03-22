<?php
if(!isset($_SESSION['admin'])){ exit; }

function admin_getForumList($all,$args)
{
	global $osimo;
	$query = "SELECT * FROM forum";
	if(is_array($args)&&count($args)>0)
	{
		$query .= buildQuery($args);
	}
	$query .= " ORDER BY title DESC";

	$result = mysql_query($query);
	
	/* Time to build the array */
	$forums = array();
	$count = 0;
	while(list($id,$category,$parent_forum,$title,$description,$views,$threads,$posts,$last_poster,$last_poster_id,$last_post_time)=mysql_fetch_row($result))
	{

		$forums[$category][$count]['id'] = $id;
		$forums[$category][$count]['parent_forum'] = $parent_forum;
		$forums[$category][$count]['title'] = $title;
		$forums[$category][$count]['description'] = $description;
		$forums[$category][$count]['views'] = $views;
		$forums[$category][$count]['threads'] = $threads;
		$forums[$category][$count]['posts'] = $posts;
		$forums[$category][$count]['last_poster'] = $last_poster;
		$forums[$category][$count]['last_poster_id'] = $last_poster_id;
		$forums[$category][$count]['last_post_time'] = $last_post_time;
			
			
		$count++;
	}
	
	if($count==0){ return false; }
	else{ return $forums; }
}

function admin_getCategoryList()
{
	$query = "SELECT * FROM category ORDER BY title ASC";
	$result = mysql_query($query);
	
	if($result)
	{
		while($category = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$categories[$category['id']]['title'] = $category['title'];
			$categories[$category['id']]['parent_forum'] = $category['parent_forum'];
		}
		
		return $categories;
	}
}

function admin_getForumNames()
{
	$query = "SELECT id,title FROM forum";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		$i=0;
		while(list($id,$title)=mysql_fetch_row($result))
		{
			$forums[$i]['id'] = $id;
			$forums[$i]['title'] = $title;
			$i++;
		}
		
		return $forums;
	}
	else
	{
		return false;
	}
}
?>