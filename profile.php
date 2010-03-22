<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	profile.php - theme user profile page loader
*	KISS - keep it simple stupid :P
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

$pageType = 'profile';
if(isset($_GET['id']))
{
	$pageID = $_GET['id'];
}
else
{
	$pageID = $_SESSION['user']['ID'];
}

$user_info = $osimo->getUserProfileInfo();

include(THEMEPATH.'profile.php');

mysql_close();
?>