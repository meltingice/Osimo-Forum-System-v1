<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	login.php - theme login page loader
*	KISS - keep it simple stupid :P
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

$pageType = 'login';
$pageID = false;

include(THEMEPATH.'login.php');

mysql_close();
?>