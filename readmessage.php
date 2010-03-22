<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	readmessage.php - theme message page loader
*	KISS - keep it simple stupid :P
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

if(!isset($_GET['id'])){ header('Location: messages.php'); exit; }

$pageType = 'message';
$pageID = $_GET['id'];

if(!$osimo->getLoggedInUser()){ header('Location: index.php'); exit; }

/* Lets get all the posts in this message thread */
$posts = $osimo->getPrivateMessagePosts();
$threadID = $_GET['id'];
$PM = true;

/* Add a pageview */
if(is_array($posts)){ $osimo->addPageView('private_message_thread',$threadID); }

$osimo->markMessageAsRead();

include(THEMEPATH.'readmessage.php');

mysql_close();
?>