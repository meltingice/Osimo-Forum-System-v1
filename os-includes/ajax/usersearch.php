<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/ajax/usersearch.php - ajax backend for jQuery autocomplete plugin and user searching
*/

session_start();
include_once('../dbconnect.php'); //connects to database
include_once('../security.php');

if($_GET['q']){ userSearch($_GET['q']); }

function userSearch($userQuery)
{
	$userQuery = secureContent($userQuery);
	
	$query = "SELECT username FROM users WHERE username LIKE '%$userQuery%'";
	$result = mysql_query($query);
	
	while(list($username)=mysql_fetch_row($result))
	{
		echo $username."\n";
	}
}
?>