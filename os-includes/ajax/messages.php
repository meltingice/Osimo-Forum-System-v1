<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/ajax/messages.php - ajax backend for private messaging system
*/

session_start();
include_once('../dbconnect.php'); //connects to database
include_once('../paths.php');
include_once('../osimo.php');
$osimo = new Osimo(); //makes magic happen

if(isset($_POST['newmessage'])){ newMessageThread($_POST['recipient'],$_POST['subj'],$_POST['content']); }
if(isset($_POST['refreshMessages'])){ refreshMessages($_POST['which']); }
if(isset($_POST['newmessagepost'])){ newMessagePost($_POST['thread'],$_POST['content'],'posts'); }
if(isset($_POST['postpreview'])){ postPreview($_POST['content']); }

function newMessageThread($recipient,$subj,$content)
{
	global $osimo;
	
	if($recipient==''||$content==''){ echo "0"; exit; }
	if($subj==''){ $subj = "No Subject"; }
	else{
		$subj = secureContent(stripslashes(rawurldecode($subj)));
	}
	
	$recipientID = $osimo->userNametoID($recipient);
	$sender = $osimo->getLoggedInUser();
	
	if($recipientID)
	{
		$query = "INSERT INTO private_message_thread (
		user_sent,
		user_received,
		title,
		time_created ) VALUES (
		'{$sender['ID']}',
		'$recipientID',
		'$subj',
		'".time()."' )";
		$result = mysql_query($query);
		if($result)
		{
			$threadID = mysql_insert_id();
			$osimo->writeToSysLog('pm-thread-create',$sender['ID'],$sender['name']." created a PM thread to $recipient (userID #$recipientID)");
			newMessagePost($threadID,$content,'threads');
		}
	}
	else
	{
		$osimo->writeToSysLog('pm-thread-fail',$sender['ID'],$sender['name']." attempted to send a PM to $recipient");
		echo "0";	
	}
}

function newMessagePost($threadID,$content,$refresh=false)
{
	global $osimo;
	$threadID = secureContent($threadID);
	$content = htmlspecialchars(secureContent(rawurldecode(html_entity_decode($content,ENT_NOQUOTES,'UTF-8'))));
	$poster = $osimo->getLoggedInUser();
	
	$query = "INSERT INTO private_message_post (
		private_message_thread,
		body,
		poster_id,
		poster_username,
		post_time ) VALUES (
		'$threadID',
		'$content',
		'{$poster['ID']}',
		'{$poster['name']}',
		'".time()."' )";
	$result = mysql_query($query);
	if($result)
	{
		/* Gotta update private_message_thread */
		$query2 = "UPDATE private_message_thread SET posts=posts+1,last_poster='{$poster['name']}',last_poster_id='{$poster['ID']}',last_post_time='".time()."',read_status='unread' WHERE id='$threadID'";
		$result2 = mysql_query($query2);
		if($result2)
		{
			if($refresh=='threads')
			{
				refreshMessages('sent');
			}
			if($refresh=='posts')
			{
				$osimo->writeToSysLog('pm-post-create',$poster['ID'],$poster['name']." replied to PM thread #$threadID");
				refreshPosts($threadID);
			}
		}
	}
}

function refreshMessages($which)
{
	global $osimo;
	$messages = $osimo->getPrivateMessageThreads($which);
	if(is_array($messages))
	{
		foreach($messages as $message)
		{
			include('../../'.THEMEPATH.'singlemessagethread.php');
		}
	}
}

function refreshPosts($threadID)
{
	global $osimo;
	$posts = $osimo->getPrivateMessagePosts($threadID);
	if(is_array($posts))
	{
		foreach($posts as $post)
		{
			include('../../'.THEMEPATH.'singlemessage.php');
		}
	}
}

function postPreview($content)
{
	include_once('../bbcode/bbparser.php');
	global $osimo;
	$user = $osimo->getLoggedInUser();
	
	$content = htmlspecialchars(secureContent(stripslashes($content)));
	
	$posts[0]['body'] = bb2html(htmlspecialchars(stripslashes(html_entity_decode($content,-1,'UTF-8'))));
	$posts[0]['poster'] = $user['ID'];
	$posts[0]['poster_username'] = $user['name'];
	$posts[0]['post_time'] = time();
	
	$postPreview = true;
	foreach($posts as $post)
	{
		include('../../'.THEMEPATH.'singlemessage.php');
	}
}