<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/forum.php - functions relating to forums
*	Functions should be accessed through the Osimo class
*/

function _getForumList($args)
{
	global $osimo;
	$query = "SELECT * FROM forum";
	if(is_array($args)&&count($args)>0)
	{
		$query .= buildQuery($args);
	}
	$query .= " ORDER BY category ASC";

	$result = mysql_query($query);
	
	/* Time to build the array */
	$forums = array();
	$count = 0;
	while($data = mysql_fetch_assoc($result)){
		if($osimo->userCanViewForum($data['id']))
		{
			$forums[$data['category']][$count]['id'] = $data['id'];
			$forums[$data['category']][$count]['parent_forum'] = $data['parent_forum'];
			$forums[$data['category']][$count]['title'] = $data['title'];
			$forums[$data['category']][$count]['description'] = $data['description'];
			$forums[$data['category']][$count]['views'] = $data['views'];
			$forums[$data['category']][$count]['threads'] = $data['threads'];
			$forums[$data['category']][$count]['posts'] = $data['posts'];
			$forums[$data['category']][$count]['last_poster'] = $data['last_poster'];
			$forums[$data['category']][$count]['last_poster_id'] = $data['last_poster_id'];
			$forums[$data['category']][$count]['last_post_time'] = $data['last_post_time'];
			
			/* Determine if the thread is new and unread */
			$forums[$data['category']][$count]['new'] = $osimo->isForumNew($data['id']);
		}
		$count++;
	}
	
	return $forums;
}

/*
*	_getCategoryName() - retrieves a category name based on its ID
*	returns string or array based on $args
*/
function _getCategoryName($args)
{
	global $osimo;
	
	if($args['id']==-1)
	{
		return 'Uncategorized';
	}
	
	$query = "SELECT title FROM category";
	if(is_array($args)&&count($args)>0)
	{
		$query .= buildQuery($args);
	}
	
	$result = $osimo->cache->sqlquery($query);
	if($result)
	{
		return $result[0]['title'];
	}
	else
	{
		foreach($result as $data){
			$category[] = $data['title'];
		}
		
		return $category;
	}
}

function _getForumName($forumID)
{
	global $osimo;
	$query = "SELECT title FROM forum WHERE id='$forumID' LIMIT 1";
	$result = $osimo->cache->sqlquery($query);
	
	if($result)
	{
		return $result[0]['title'];
	}
	else
	{
		return false;
	}
}

function _isForumNew($forumID)
{
	global $osimo;
	$forumID = secureContent($forumID);
	
	if($osimo->getLoggedInUser())
	{
		/* First, check to see if the forum is in the osimo array */
		if(isset($_SESSION['osimo']['read_forums'])&&in_array($forumID,$_SESSION['osimo']['read_forums']))
		{
			return false;
		}
		
		/* Thread isn't in osimo array, lets check the database */
		$query = "SELECT last_post_time FROM forum WHERE id='$forumID' LIMIT 1";
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
?>