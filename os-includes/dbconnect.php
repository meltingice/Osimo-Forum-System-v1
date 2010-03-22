<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/dbconnect.php - connects to the MySQL Database
*	Don't forget to edit this file for your own server!
*/
include('bans.php');

$mysqlhost = "localhost";
$mysqluser = "Osimo";
$mysqlpass = "pass";
$mysqldb = "osimo";
$mysqlconnect = @mysql_connect($mysqlhost, $mysqluser, $mysqlpass) or die('Could not connect to MySQL Server!');
$mysqlselectdb = @mysql_select_db($mysqldb)or die('Could not select database!');

/* Ban check */	
if(!strpos($_SERVER['SCRIPT_NAME'],'logout.php') && isset($_SESSION['user']) && !allowLogin($_SESSION['user']['ID'])){
	header('Location: logout.php?banned=true'); exit;
}
?>