<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/post.php - functions relating to posts
*	Functions should be accessed through the Osimo class
*/

function _getPostList($num,$page,$args)
{
	global $osimo;
	include_once('bbcode/bbparser.php');
	
	$forum = $osimo->getThreadForum($args['thread']);
	if($osimo->userCanViewThreads($forum['id']))
	{
		$num = secureContent($num);
		$lower = ($page-1)*$num;
		
		if(is_array($args)&&count($args)>0)
		{
			$where = substr(buildQuery($args),6);
		}
		else{
			$where = false;
		}
		
		$sort = "id ASC";
		$limit = "$lower,$num";
		
		$posts = $osimo->cache->getPosts($where,$sort,$limit);
		if(!$posts){ return false; }
		foreach($posts as $key=>$post){
			$posts[$key]['body'] = bb2html($post['body']);
		}
		
		/* Make this thread not "new" */
		if(is_array($_SESSION['osimo']['read_threads']))
		{
			if(!in_array($args['thread'],$_SESSION['osimo']['read_threads']))
			{
				$_SESSION['osimo']['read_threads'][] = $args['thread'];
			}
		}
		else
		{
			$_SESSION['osimo']['read_threads'][] = $args['thread'];
		}
		
		if(is_array($_SESSION['osimo']['read_forums'])&&in_array($forum['id'],$_SESSION['osimo']['read_forums']))
		{
			/* Do nothing */
		}
		else
		{
			/* Check to see if there are any "new" threads in this forum still */
			$query2 = "SELECT id FROM thread WHERE forum='{$forum['id']}' AND last_post_time>'{$_SESSION['user']['last_login']}'";
			$result2 = mysql_query($query2);
			if($result2)
			{
				$newThreads = false;
				if(mysql_num_rows($result2)>0)
				{
					while(list($id)=mysql_fetch_row($result2))
					{
						if(!in_array($id,$_SESSION['osimo']['read_threads']))
						{
							$newThreads = true;
						}
					}
					
				}
				if(!$newThreads)
				{
					$_SESSION['osimo']['read_forums'][] = $forum['id'];
				}
			}
		}
	}

	return $posts;
}

function _getPostThread($postID)
{
	$postID = secureContent($postID);
	
	$query = "SELECT thread FROM post WHERE id='$postID' LIMIT 1";
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

function _getPostPermalink($postID,$threadID,$page)
{
	return OSIMOPATH."thread.php?id=$threadID&page=$page#post-$postID";
}

function _getPostLocation($post_id){
	global $osimo;
	$post_id = secureContent($post_id);
	
	$query = "SELECT id FROM post WHERE thread=(SELECT thread FROM post WHERE id='$post_id' LIMIT 1) ORDER BY id ASC";
	$result = mysql_query($query);
	if($result && mysql_num_rows($result)>0){
		$i=0;
		while($data = mysql_fetch_assoc($result)){
			$i++;
			if($data['id'] == $post_id){
				break;
			}
		}
		
		$loc['page'] = ceil($i / 10);
		$loc['post'] = $post_id;
		$loc['thread'] = $osimo->getPostThread($post_id);
		
		return $loc;
	}
	
	return false;
}
?>