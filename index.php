<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	index.php - theme index page loader
*	KISS - keep it simple stupid :P
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

$pageType = 'index';
$pageID = false;

/* First, lets retrieve all the first-level forums */
$forums = $osimo->getForumList('parent_forum=-1');
$osimo->setUserOnline();
$osimo->addPageView('other',0);

include(THEMEPATH.'index.php');

mysql_close();
?>