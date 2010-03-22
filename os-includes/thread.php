<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/thread.php - functions relating to threads
*	Functions should be accessed through the Osimo class
*/

function _getThreadList($num,$page,$sticky,$args)
{
	global $osimo;
	if($args['forum']==-1){ return false; }
	if($osimo->userCanViewThreads($args['forum']) && $osimo->userCanViewForum($args['forum']))
	{
		if($num==-1){ $num = $osimo->option_threadNumPerPage(); }
		$num = secureContent($num);
		$lower = ($page-1)*$num;
		
		$query = "SELECT * FROM thread";
		if(is_array($args)&&count($args)>0)
		{
			$query .= buildQuery($args);
		}
		
		/* Sticky threads ? */
		if($sticky=='true')
		{
			$query .= " ORDER BY sticky DESC, last_post_time";
		}
		if($sticky=='false')
		{
			$query .= " AND sticky='0' ORDER BY last_post_time";
		}
		if($sticky=='only')
		{
			$query .= " AND sticky='1' ORDER BY last_post_time";
		}
		
		$query .= " DESC LIMIT $lower,$num";
		$result = mysql_query($query);
		if($result){
			$i=0;
			while($data = mysql_fetch_assoc($result)){
				$threads[$i] = $data;
				$threads[$i]['new'] = $osimo->isThreadNew($threads[$i]['id']);
				$i++;
			}
			
			return $threads;
		}
		else{
			return false;
		}
	}
}

function _getThreadForum($threadID)
{
	global $osimo;
	$threadID = secureContent($threadID);
	$query = "SELECT thread.forum AS id, forum.title FROM thread LEFT JOIN forum ON (thread.forum = forum.id) WHERE thread.id='$threadID' LIMIT 1";
	$result = $osimo->cache->sqlquery($query);
	if($result){ return $result[0]; }
	
	return false;
}

function _getThreadName($threadID)
{
	$threadID = secureContent($threadID);
	$query = "SELECT title FROM thread WHERE id='$threadID' LIMIT 1";

	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		return reset(mysql_fetch_row($result));
	}
	else
	{
		return false;
	}
}

function _isThreadSticky($threadID)
{
	$threadID = secureContent($threadID);
	
	$query = "SELECT sticky FROM thread WHERE id='$threadID' LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		return reset(mysql_fetch_row($result));
	}
	else
	{
		return false;
	}
}

function _isThreadLocked($threadID)
{
	$threadID = secureContent($threadID);
	
	$query = "SELECT locked FROM thread WHERE id='$threadID' LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		return reset(mysql_fetch_row($result));
	}
	else
	{
		return false;
	}
}

function _isThreadNew($threadID)
{
	global $osimo;
	$threadID = secureContent($threadID);
	
	if($osimo->getLoggedInUser())
	{
		/* First, check to see if the thread is in the osimo array */
		if(isset($_SESSION['osimo']['read_threads'])&&in_array($threadID,$_SESSION['osimo']['read_threads']))
		{
			return false;
		}
		
		/* Thread isn't in osimo array, lets check the database */
		$query = "SELECT last_post_time FROM thread WHERE id='$threadID' LIMIT 1";
		$result = mysql_query($query);
		
		if($result&&mysql_num_rows($result)>0)
		{
			$last_read = reset(mysql_fetch_row($result));
			if($last_read>$_SESSION['user']['last_login'])
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function _getRecentlyUpdatedThreads($num)
{
	global $osimo;
	$num = secureContent($num);
	$query = "SELECT id,forum,title,original_poster,original_poster_id,last_poster,last_poster_id,last_post_time,posts,views FROM thread ORDER BY last_post_time DESC LIMIT $num";
	$result = mysql_query($query);
	if($result&&mysql_num_rows($result))
	{
		while($data = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			if($osimo->userCanViewForum($data['forum'])&&$osimo->userCanViewThreads($data['forum']))
			{
				$threads[$data['id']]['title'] = $data['title'];
				$threads[$data['id']]['original_poster'] = $data['original_poster'];
				$threads[$data['id']]['original_poster_id'] = $data['original_poster_id'];
				$threads[$data['id']]['last_poster'] = $data['last_poster'];
				$threads[$data['id']]['last_poster_id'] = $data['last_poster_id'];
				$threads[$data['id']]['last_post_time'] = $data['last_post_time'];
				$threads[$data['id']]['posts'] = $data['posts'];
				$threads[$data['id']]['views'] = $data['views'];
			}
		}
		
		return $threads;
	}
	
	return false;
}
?>