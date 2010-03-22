<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/ajax/post.php - ajax backend for posts
*/
session_start();
include_once('../dbconnect.php'); //connects to database
include_once('../paths.php');
include_once('../osimo.php');
$osimo = new Osimo(); //makes magic happen

if(isset($_POST['newpost'])){ addPost($_POST['threadID'],$_POST['postContent'],true); }
if(isset($_POST['refresh'])){ refreshPosts($_POST['threadID'],$_POST['page']); }
if(isset($_POST['loadpage'])){ refreshPosts($_POST['threadID'],$_POST['page']); }
if(isset($_POST['postpreview'])){ postPreview($_POST['content']); }
if(isset($_POST['postedit'])){ editPost($_POST['postID'],$_POST['content']); }
if(isset($_POST['deletePost'])){ deletePost($_POST['deletePost']); }
if(isset($_POST['reportPost'])){ reportPost($_POST['reportPost']); }
if(isset($_POST['getQuote'])){ getQuote($_POST['getQuote']); }

function addPost($threadID,$content,$showposts=false)
{
	global $osimo;
	$forum = $osimo->getThreadForum($threadID);
	if($osimo->userCanPostReply($forum['id'])&&!$osimo->isThreadLocked($threadID))
	{
		$threadID = secureContent(stripslashes($threadID));
		$content = htmlspecialchars(secureContent(rawurldecode(html_entity_decode($content,ENT_NOQUOTES,'UTF-8'))));
		$user = $osimo->getLoggedInUser();
		
		if($content==''){ exit; }
		
		if(!$osimo->floodControl('post')){ echo "wait"; exit; }
		
		$query = "INSERT INTO post (
			thread,
			body,
			poster_id,
			poster_username,
			post_time ) VALUES (
			'$threadID',
			'$content',
			'{$user['ID']}',
			'{$user['name']}',
			'".time()."' )";
			
		$result = mysql_query($query);
		if($result)
		{
			$postID = mysql_insert_id();
			/* Post created, time to update thread table */
			$query2 = "UPDATE thread SET posts=posts+1,last_poster='{$user['name']}',last_poster_id='{$user['ID']}',last_post_time='".time()."' WHERE id='$threadID'";
			$result2 = mysql_query($query2);
			
			/* Gotta update forum table too */
			$query3 = "UPDATE forum SET posts=posts+1,last_poster='{$user['name']}',last_poster_id='{$user['ID']}',last_post_time='".time()."' WHERE id='".$forum['id']."'";
			$result3 = mysql_query($query3);
			
			/* users table next */
			$query4 = "UPDATE users SET time_last_post='".time()."',posts=posts+1 WHERE id='{$user['ID']}' LIMIT 1";
			$result4 = mysql_query($query4);
			
			/* finally the stats table */
			$today = $osimo->getTodayTimestamp();
			$query3 = "SELECT COUNT(*) FROM stats WHERE date='$today' AND forumID='{$forum['id']}' AND type='posts' LIMIT 1";
			$result3 = mysql_query($query3);
			if($result3)
			{
				if(reset(mysql_fetch_row($result3))==0)
				{
					$insert = "INSERT INTO stats (forumID,date,type,count) VALUES ('{$forum['id']}',$today,'posts','1')";
					$result4 = mysql_query($insert);
				}
				else
				{
					$update = "UPDATE stats SET count=count+1 WHERE forumID='{$forum['id']}' AND date='$today' AND type='posts' LIMIT 1";
					$result4 = mysql_query($update);
				}
			}
			
			if(@!in_array($threadID,$_SESSION['osimo']['read_threads']))
			{
				$_SESSION['osimo']['read_threads'][] = $threadID;
			}
		}
		if($result&&$showposts)
		{
			$osimo->writeToSysLog('post-create',$user['ID'],$user['name']." created post #$postID in thread #$threadID");
			$pages = $osimo->getPagination('table=post',"thread=$threadID");
			refreshPosts($threadID,$pages);
		}
		if($result&&!$showposts)
		{
			return true;
		}
		if(!$result)
		{
			echo "0";
		}
	}
	else
	{
		$user = $osimo->getLoggedInUser();	
		$osimo->writeToSysLog('attempted-breach',$user['ID'],$user['name']." attempted to post to thread #$threadID");
		echo "0";
	}
}

function editPost($postID,$content)
{
	global $osimo;
	$postID = secureContent($postID);
	$content = htmlspecialchars(rawurldecode(html_entity_decode($content,ENT_NOQUOTES,'UTF-8')));
	$user = $osimo->getLoggedInUser();
	
	$options = array(
		"body"=>$content,
		"last_edit_user_id"=>$user['ID'],
		"last_edit_username"=>$user['name'],
		"last_edit_time"=>time()
	);
	
	$result = $osimo->cache->updatePost($postID,$options);
	
	if($result)
	{
		$osimo->writeToSysLog('edit',$user['ID'],$user['name']." edited post #$postID");
		echo "1";
	}
	else
	{
		$osimo->writeToSysLog('attempted-breach',$user['ID'],$user['name']." attempted to edit post #$postID");
		echo "0";
	}
}

function refreshPosts($threadID,$page=1)
{
	global $osimo;
	$threadName = $osimo->getThreadName($threadID);
	$num = $osimo->option_postNumPerPage();
	$posts = $osimo->getPostList("num=$num","page=$page","thread=$threadID");
	$forum = $osimo->getThreadForum($threadID);
	
	foreach($posts as $post)
	{
		include('../../'.THEMEPATH.'singlepost.php');
	}
}

function postPreview($content)
{
	include_once('../bbcode/bbparser.php');
	global $osimo;
	$user = $osimo->getLoggedInUser();
	//$content = htmlspecialchars(secureContent(rawurldecode(html_entity_decode($content,ENT_NOQUOTES,'UTF-8'))));
	$posts[0]['body'] = bb2html(htmlspecialchars(rawurldecode(html_entity_decode($content,ENT_NOQUOTES,'UTF-8'))));
	$posts[0]['poster_id'] = $user['ID'];
	$posts[0]['poster_username'] = $user['name'];
	$posts[0]['post_time'] = time();
	
	$postPreview = true;
	foreach($posts as $post)
	{
		include('../../'.THEMEPATH.'singlepost.php');
	}
}

function getPostContent($id,$echo=false)
{
	global $osimo;
	$id = secureContent($id);
	
	$result = $osimo->cache->getPosts("id='$id'",false,"1");
	
	if($result)
	{
		if($echo){
			echo $result[0]['body'];
		}
		else{
			return $result[0]['body'];
		}
	}
	else
	{
		return false;
	}
}

function getQuote($id){
	global $osimo;
	$id = secureContent($id);
	
	$result = $osimo->cache->getPosts("id='$id'",false,"1");
	if($result){
		$data = $result[0];
		echo json_encode(array("username"=>$data['poster_username'],"content"=>$data['body'])); exit;
	}
	else{
		echo json_encode(array("error"=>"Error retrieving post for quoting")); exit;
	}
}

function deletePost($postID)
{
	global $osimo;
	
	$user = $osimo->getLoggedInUser();	
	if(!$osimo->userIsModerator()&&!$osimo->userIsAdmin())
	{
		$osimo->writeToSysLog('attempted-breach',$user['ID'],$user['name']." attempted to delete post #$postID");
		echo "0";
		exit;
	}
	
	$postID = secureContent($postID);
	$threadID = $osimo->getPostThread($postID);
	$forum = $osimo->getThreadForum($threadID);
	
	/* First lets get some info */
	$query = "SELECT poster_id FROM post WHERE id='$postID' LIMIT 1";
	$result = mysql_query($query);
	if($result){ $userID = reset(mysql_fetch_row($result)); }
	mysql_error();
	
	/* Now, lets remove the post */
	$delQuery = "DELETE FROM post WHERE id='$postID' LIMIT 1";
	$delResult = mysql_query($delQuery);
	mysql_error();
	
	/* Next lets update the thread table */
	$query1 = "UPDATE thread SET posts=posts-1, last_poster=(SELECT poster_username FROM post WHERE thread='$threadID' ORDER BY post_time DESC LIMIT 1), last_poster_id=(SELECT poster_id FROM post WHERE thread='$threadID' ORDER BY post_time DESC LIMIT 1), last_post_time=(SELECT post_time FROM post WHERE thread='$threadID' ORDER BY post_time DESC LIMIT 1) WHERE id='$threadID' LIMIT 1";
	$result1 = mysql_query($query1);
	mysql_error();
	
	/* and the forum table */
	$query2 = "UPDATE forum SET posts=posts-1, last_poster=(SELECT last_poster FROM thread WHERE forum='{$forum['id']}' ORDER BY last_post_time DESC LIMIT 1), last_poster_id=(SELECT last_poster_id FROM thread WHERE forum='{$forum['id']}' ORDER BY last_post_time DESC LIMIT 1), last_post_time=(SELECT last_post_time FROM thread WHERE forum='{$forum['id']}' ORDER BY last_post_time DESC LIMIT 1) WHERE id='{$forum['id']}' LIMIT 1";
	$result2 = mysql_query($query2);
	mysql_error();
	
	/* Finally the users table */
	$query3 = "UPDATE users SET posts=posts-1 WHERE id='$userID' LIMIT 1";
	$result3 = mysql_query($query3);
	mysql_error();
	
	if($result)
	{
		$osimo->writeToSysLog('post-delete',$user['ID'],$user['name']." deleted post #$postID");
		echo $threadID;
	}
	else{ echo "0"; }
}

function reportPost($post_id){
	global $osimo;
	$user = $osimo->getLoggedInUser();
	if(!$user){ echo json_encode(array("success"=>0,"error"=>"You must be logged in to report posts!")); exit; }
	
	$post_id = secureContent($post_id);
	
	$query0 = "SELECT COUNT(*) FROM reports WHERE post_id='$post_id' AND reporter_id='{$user['ID']}' LIMIT 1";
	$result0 = mysql_query($query0);
	if($result0 && mysql_num_rows($result0) > 0){
		if(reset(mysql_fetch_row($result0))){
			echo json_encode(array("success"=>0,"error"=>"You have already reported this post!")); exit;
		}
	}
	
	$query = "INSERT INTO reports (
		post_id,
		reporter_id,
		reporter_name,
		report_time
		) VALUES (
		'$post_id',
		'{$user['ID']}',
		'{$user['name']}',
		'".time()."')";
	$result = mysql_query($query);
	if($result){
		$query2 = "SELECT COUNT(*) FROM reports WHERE post_id='$post_id'";
		$result2 = mysql_query($query2);
		if($result2){
			$count = reset(mysql_fetch_row($result2));
			echo json_encode(array("success"=>1,"content"=>"Post reported! You will not see any visual notice for this besides this one.\nThis post has been reported $count times including this time.")); exit;
		}
	}
}
?>