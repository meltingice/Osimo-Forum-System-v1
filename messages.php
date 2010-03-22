<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	messages.php - theme messages page loader
*	KISS - keep it simple stupid :P
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

if(!$osimo->getLoggedInUser()){ header('Location: index.php'); exit; }

$pageType = 'inbox';
$pageID = $_SESSION['user']['ID'];

include(THEMEPATH.'messages.php');

mysql_close();
?>