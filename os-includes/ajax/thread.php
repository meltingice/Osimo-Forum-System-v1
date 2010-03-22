<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/ajax/thread.php - ajax backend for threads
*/

session_start();
include_once('../dbconnect.php'); //connects to database
include_once('../paths.php');
include_once('../osimo.php');
$osimo = new Osimo(); //makes magic happen

if(isset($_POST['newthread'])){ addThread($_POST['forumID'],$_POST['threadTitle'],$_POST['threadDescription'],$_POST['postContent']); }
if(isset($_POST['loadpage'])){ refreshThreads($_POST['forumID'],$_POST['sticky'],$_POST['page']); }
if(isset($_POST['sticky'])&&!isset($_POST['loadpage'])){ stickyThread($_POST['threadID'],$_POST['sticky']); }
if(isset($_POST['lock'])&&!isset($_POST['loadpage'])){ lockThread($_POST['threadID'],$_POST['lock']); }
if(isset($_POST['deleteThread'])){ deleteThread($_POST['deleteThread']); }
if(isset($_POST['moveThread'])){ moveThread($_POST['moveThread'],$_POST['destForum']); }

function addThread($forumID,$title,$description,$content)
{
	global $osimo;
	
	if(!$osimo->floodControl('thread')){ echo "wait"; exit; }
	
	if($osimo->userCanPostThread($forumID)&&$osimo->userCanPostReply($forumID))
	{
		include_once('post.php');
		$forumID = secureContent($forumID);
		$title = secureContent(stripslashes(rawurldecode($title)));
		$description = secureContent(stripslashes(rawurldecode($description)));
		
		if($title==''||$content==''){ exit; }
		
		$user = $osimo->getLoggedInUser();
		
		/* First create the thread */
		$query = "INSERT INTO thread (
			forum,
			title,
			description,
			posts,
			original_poster,
			original_poster_id,
			original_post_time,
			last_poster,
			last_poster_id,
			last_post_time ) VALUES (
			'$forumID',
			'$title',
			'$description',
			'0',
			'{$user['name']}',
			'{$user['ID']}',
			'".time()."',
			'{$user['name']}',
			'{$user['ID']}',
			'".time()."' )";
		$result = mysql_query($query);
		if($result)
		{
			$threadID = mysql_insert_id();
			
			/* Thread was created, lets update forum table */
			$query2 = "UPDATE forum SET threads=threads+1,last_poster='{$user['name']}',last_poster_id='{$user['ID']}',last_post_time='".time()."' WHERE id='$forumID'";
			$result2 = mysql_query($query2);
			
			/* Update statistics */
			$today = $osimo->getTodayTimestamp();
			$query3 = "SELECT COUNT(*) FROM stats WHERE date='$today' AND forumID='$forumID' AND type='threads' LIMIT 1";
			$result3 = mysql_query($query3);
			if($result3)
			{
				if(reset(mysql_fetch_row($result3))==0)
				{
					$insert = "INSERT INTO stats (forumID,date,type,count) VALUES ($forumID,$today,'threads','1')";
					$result4 = mysql_query($insert);
				}
				else
				{
					$update = "UPDATE stats SET count=count+1 WHERE forumID='$forumID' AND date='$today' AND type='threads' LIMIT 1";
					$result4 = mysql_query($update);
				}
			}
			
			/* Finally, add the post to the thread */
			$post = addPost($threadID,$content);
			if($post)
			{
				$osimo->writeToSysLog('thread-create',$user['ID'],$user['name']." created thread #$threadID in forum #$forumID");
				refreshThreads($forumID,'true');
			}
		}
		else
		{
			echo "0";
		}
	}
	else
	{
		$user = $osimo->getLoggedInUser();	
		$osimo->writeToSysLog('attempted-breach',$user['ID'],$user['name']." attempted to add a thread in forum #$forumID");
		echo "0";
	}
}

function refreshThreads($forumID,$sticky,$page=1)
{
	global $osimo;
	$threads = $osimo->getThreadList("forum=$forumID","page=$page","sticky=$sticky");
	
	foreach($threads as $thread)
	{
		include('../../'.THEMEPATH.'singlethread.php');
	}
}

function stickyThread($id,$sticky)
{
	global $osimo;
	$id = secureContent($id);
	
	$user = $osimo->getLoggedInUser();	
	if(!$osimo->userIsModerator()&&!$osimo->userIsAdmin())
	{
		$osimo->writeToSysLog('attempted-breach',$user['ID'],$user['name']." attempted to sticky thread #$id");
		echo "0";
		exit;
	}
	
	$query = "UPDATE thread SET sticky='";
	if($sticky=='true'){ $query .= "1"; }
	else{ $query .= "0"; }
	$query .= "' WHERE id='$id' LIMIT 1";
	
	$result = mysql_query($query);
	
	if($result)
	{
		$osimo->writeToSysLog('thread-sticky',$user['ID'],$user['name']." stickied thread #$id");
		echo "1";
	}
	else{ echo "0"; }
}

function lockThread($id,$lock)
{
	global $osimo;
	$id = secureContent($id);
	
	$user = $osimo->getLoggedInUser();	
	if(!$osimo->userIsModerator()&&!$osimo->userIsAdmin())
	{
		$osimo->writeToSysLog('attempted-breach',$user['ID'],$user['name']." attempted to lock thread #$id");
		echo "0";
		exit;
	}
	
	$query = "UPDATE thread SET locked='";
	if($lock=='true'){ $query .= "1"; }
	else{ $query .= "0"; }
	$query .= "' WHERE id='$id' LIMIT 1";
	
	$result = mysql_query($query);
	
	if($result)
	{
		$osimo->writeToSysLog('thread-lock',$user['ID'],$user['name']." locked thread #$id");
		echo "1";
	}
	else{ echo "0"; }
}

function deleteThread($threadID)
{
	global $osimo;

	$user = $osimo->getLoggedInUser();	
	if(!$osimo->userIsModerator()&&!$osimo->userIsAdmin())
	{
		$osimo->writeToSysLog('attempted-breach',$user['ID'],$user['name']." attempted to delete thread #$threadID");
		echo "0";
		exit;
	}
	
	$threadID = secureContent($threadID);
	$forum = $osimo->getThreadForum($threadID);
	
	/* 
	* First we have to get all the users who have posted 
	* in this thread, count up the # of times they posted,
	* then subtract this number from their total # of posts.
	*/
	$query = "SELECT poster_id FROM post WHERE thread='$threadID'";
	$result = mysql_query($query);
	if($result)
	{
		$users = array();
		$posts = 0;
		while(list($poster_id)=mysql_fetch_row($result))
		{
			$users[$poster_id]++;
			$posts++;
		}
		
		foreach($users as $userID=>$num)
		{
			$numQuery = "UPDATE users SET posts=posts-$num WHERE id='$userID' LIMIT 1";
			$numResult = mysql_query($numQuery);
		}
	}
	
	/* Time to delete the thread */
	$query4 = "DELETE FROM thread WHERE id='$threadID' LIMIT 1";
	$result4 = mysql_query($query4);
	
	$query5 = "DELETE FROM post WHERE thread='$threadID'";
	$result5 = mysql_query($query5);
	
	/*
	* Next we need to update the forum table so that the last
	* updated thread is no longer this one.
	*/
	$query2 = "SELECT last_poster,last_poster_id,last_post_time FROM thread ORDER BY last_post_time DESC LIMIT 1";
	$result2 = mysql_query($query2);
	if($result2)
	{
		while($data = mysql_fetch_array($result2,MYSQL_ASSOC))
		{
			$query3 = "UPDATE forum SET last_poster='{$data['last_poster']}', last_poster_id='{$data['last_poster_id']}', last_post_time='{$data['last_post_time']}', threads=threads-1, posts=posts-$posts WHERE id='{$forum['id']}'";
			$result3 = mysql_query($query3);
		}
	}
	
	if($result4)
	{
		$osimo->writeToSysLog('thread-delete',$user['ID'],$user['name']." deleted thread #$threadID");
		echo "1";
	}
	else{ echo "0"; }
}

function moveThread($threadID,$destForum)
{
	global $osimo;
	
	if(!$osimo->userIsAdmin()&&!$osimo->userIsModerator()){ echo "0"; exit; }
	
	$threadID = secureContent($threadID);
	$destForum = secureContent($destForum);
	
	$query0 = "SELECT forum,posts FROM thread WHERE id='$threadID'";
	$result0 = mysql_query($query0);
	if($result0&&mysql_num_rows($result0)>0)
	{
		while($data = mysql_fetch_array($result0,MYSQL_ASSOC))
		{
			$oldForum = $data['forum'];
			$posts = $data['posts'];
		}
	}
	
	/* First, update the thread to the new forum */
	$query = "UPDATE thread SET forum='$destForum' WHERE id='$threadID' LIMIT 1";
	$result = mysql_query($query);
	
	/* Now lets cleanup some stuff */
	$query2 = "SELECT last_poster,last_poster_id,last_post_time FROM thread WHERE forum='$oldForum' ORDER BY last_post_time DESC LIMIT 1";
	$result2 = mysql_query($query2);
	if($result2)
	{
		while($data = mysql_fetch_array($result2,MYSQL_ASSOC))
		{
			$query3 = "UPDATE forum SET last_poster='{$data['last_poster']}', last_poster_id='{$data['last_poster_id']}', last_post_time='{$data['last_post_time']}', threads=threads-1, posts=posts-$posts WHERE id='$oldForum'";
			$result3 = mysql_query($query3);
		}
	}
	
	if($result3)
	{
		$user = $osimo->getLoggedInUser();
		$osimo->writeToSysLog('thread-move',$user['ID'],$user['name']." moved thread #$threadID from forum #$oldForum to #$destForum");
		echo "1";
	}
	else
	{
		echo "0";
	}
}
?>