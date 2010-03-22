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

if(isset($_GET['type'])&&$_GET['type']!=''){ $type = $_GET['type']; }
else{ $type = 'contents'; }
if(isset($_GET['page'])&&$_GET['page']!=''){ $page = $_GET['page']; }
else{ $page = 1; }

if(isset($_GET['q'])&&$_GET['q']!='')
{
	$query = $_GET['q'];
	$search = $osimo->getSearchResults($query, $type, $page, $numSearchPages);
}

include(THEMEPATH.'search.php');

mysql_close();
?> 