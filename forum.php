<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	forum.php - theme forum page loader
*	KISS - keep it simple stupid :P
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

if(!isset($_GET['id'])){ header('Location: index.php'); exit; }

/* First we need to see what the parent forum is */
$forumID = $osimo->getParentForum();

/* Set some data for the header */
$pageType = 'forum';
$pageID = $forumID;

/* Next, lets retrieve all the subforums (if any) */
$forums = $osimo->getForumList("parent_forum=$forumID");

if($osimo->areForums($forums))
{
	/* Add a pageview */
	$osimo->addPageView('forum',$forumID);
	
	/*
	*	Next we need to get the categories, and we do so
	*	by using the getCategories() function.  This gives us the ID's
	*	of the categories.
	*/
	$categories = $osimo->getCategories($forums);
}

if(is_numeric($_GET['page'])){ $page = $_GET['page']; }
else{ $page = 1; }

include(THEMEPATH.'forum.php');

mysql_close();
?>