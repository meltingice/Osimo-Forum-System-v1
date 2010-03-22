<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	thread.php - theme thread page loader
*	KISS - keep it simple stupid :P
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

if(!isset($_GET['id'])){ header('Location: index.php'); exit; }

/* First, lets check and see what thread we're viewing */
$threadID = $osimo->getThreadID();
$threadName = $osimo->getThreadName($threadID);

$pageType = 'thread';
$pageID = $threadID;

/* 
*	Lets get the list of posts in this thread
*	Limit # of posts to value set in MySQL options table
*/
$num = $osimo->option_postNumPerPage();
if(is_numeric($_GET['page'])){ $page = $_GET['page']; }
else{ $page = 1; }

$posts = $osimo->getPostList("num=$num","thread=$threadID","page=$page");

if(is_array($posts)){ $osimo->addPageView('thread',$threadID); }

include(THEMEPATH.'thread.php');

mysql_close();
?>