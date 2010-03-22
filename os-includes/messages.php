<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/messages.php - functions relating to private messaging
*	Functions should be accessed through the Osimo class
*/

function _getPrivateMessageThreads($which)
{
	global $osimo;

	if($which!='sent'&&$which!='inbox'){ exit; }
	
	$user = $osimo->getLoggedInUser();
	
	if($osimo->userCanReceivePM())
	{
		$query = "SELECT id,user_sent,user_received,title,views,posts,time_created,last_poster,last_poster_id,last_post_time,read_status FROM private_message_thread WHERE";
		if($which=='inbox')
		{
			$query .= " user_received='{$user['ID']}' OR (user_sent='{$user['ID']}' AND last_post_time!=time_created) ORDER BY read_status DESC,last_post_time DESC";
		}
		if($which=='sent')
		{
			$query .= " user_sent='{$user['ID']}' OR (user_received='{$user['ID']}' AND last_poster_id='{$user['ID']}') ORDER BY last_post_time DESC";
		}
		
		$result = mysql_query($query);
		
		if($result&&mysql_num_rows($result)>0)
		{
			$i=0;
			while(list($id,$user_sent,$user_received,$title,$views,$posts,$time_created,$last_poster,$last_poster_id,$last_post_time,$read_status)=mysql_fetch_row($result))
			{
				$pms[$i]['id'] = $id;
				$pms[$i]['user_sent']['ID'] = $user_sent;
				$pms[$i]['user_sent']['name'] = $osimo->userIDtoName($user_sent);
				$pms[$i]['user_received']['ID'] = $user_received;
				$pms[$i]['user_received']['name'] = $osimo->userIDtoName($user_received);
				$pms[$i]['title'] = $title;
				$pms[$i]['views'] = $views;
				$pms[$i]['posts'] = $posts;
				$pms[$i]['time_created'] = $time_created;
				$pms[$i]['last_poster'] = $last_poster;
				$pms[$i]['last_poster_id'] = $last_poster_id;
				$pms[$i]['last_post_time'] = $last_post_time;
				$pms[$i]['read_status'] = $read_status;
				if($which=='received')
				{
					if($read_status=='unread'&&$user_sent!=$user['ID']){ $pms[$i]['new'] = true; }
					else{ $pms[$i]['new'] = false; }
				}
				else
				{
					$pms[$i]['new'] = false;
				}
				$i++;
			}
			
			return $pms;
		}
		else
		{
			return false;
		}
	}
}

function _getPrivateMessagePosts($id)
{
	global $osimo;
	
	if($osimo->userCanReceivePM())
	{
		include_once(ABS_INCLUDES.'bbcode/bbparser.php');
		$id = secureContent($id);
		$user = $osimo->getLoggedInUser();
		
		$query1 = "SELECT user_sent,user_received FROM private_message_thread WHERE id='$id' LIMIT 1";
		$result1 = mysql_query($query1);
		if($result1&&mysql_num_rows($result1)>0)
		{
			while(list($user_sent,$user_received)=mysql_fetch_row($result1))
			{
				if($user_sent!=$user['ID']&&$user_received!=$user['ID']){ exit; }
			}
		}
		
		$query2 = "SELECT id,body,poster_id,poster_username,post_time FROM private_message_post WHERE private_message_thread='$id' ORDER BY post_time ASC";
		$result2 = mysql_query($query2);
		
		if($result2&&mysql_num_rows($result2)>0)
		{
			$i=0;
			while(list($id,$body,$poster_id,$poster_username,$post_time)=mysql_fetch_row($result2))
			{
				$posts[$i]['id'] = $id;
				$posts[$i]['body'] = bb2html($body);
				$posts[$i]['poster'] = $poster_id;
				$posts[$i]['poster_username'] = $poster_username;
				$posts[$i]['post_time'] = $post_time;
				$i++;
			}
			
			return $posts;
		}
	}
	
	return false;
}

function _getMessageTitle($id)
{
	$id = secureContent($id);
	
	$query = "SELECT title FROM private_message_thread WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		return reset(mysql_fetch_row($result));
	}
	
	return false;
}

function _getNumUnreadMessages()
{
	global $osimo;
	$user = $osimo->getLoggedInUser();
	
	$query = "SELECT COUNT(*) FROM private_message_thread WHERE (user_received='{$user['ID']}' OR user_sent='{$user['ID']}') AND read_status='unread' AND last_poster_id!='{$user['ID']}'";
	$result = mysql_query($query);
	
	return reset(mysql_fetch_row($result));
}

function _markMessageAsRead($id)
{
	global $osimo;
	$id = secureContent($id);
	$user = $osimo->getLoggedInUser();
	
	$query = "SELECT read_status FROM private_message_thread WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	
	if($result)
	{
		$readstatus = reset(mysql_fetch_row($result));
		if($readstatus=='unread')
		{
			$query2 = "UPDATE private_message_thread SET read_status=(SELECT CASE last_poster_id WHEN {$user['ID']} THEN 'unread' ELSE 'read' END) WHERE id='$id' LIMIT 1";
			$result2 = mysql_query($query2);
		}
	}
	
	if($result||$result2){ return true; }
	else{ return false; }
}

function _getMessageUsers($id)
{
	global $osimo;
	
	$id = secureContent($id);
	
	$query = "SELECT user_sent,user_received FROM private_message_thread WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	
	if($result)
	{
		while($data = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			$users['user_sent']['ID'] = $data['user_sent'];
			$users['user_sent']['name'] = $osimo->userIDtoName($data['user_sent']);
			$users['user_received']['ID'] = $data['user_received'];
			$users['user_received']['name'] = $osimo->userIDtoName($data['user_received']);
		}
		
		return $users;
	}
}
?>