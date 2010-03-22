<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-admin/includes/ajax/modsearch.php - ajax backend for jQuery autocomplete plugin and moderator/admin searching
*/

session_start();
if(!isset($_SESSION['admin'])){ exit; }
include_once('../../../os-includes/dbconnect.php');
include_once('../../../os-includes/security.php');

if($_GET['q']){ modSearch($_GET['q']); }

function modSearch($userQuery)
{
	$userQuery = secureContent($userQuery);
	
	$query = "SELECT username FROM users WHERE username LIKE '%$userQuery%' AND (is_admin='1' OR is_global_mod='1')";
	$result = mysql_query($query);
	
	while(list($username)=mysql_fetch_row($result))
	{
		echo $username."\n";
	}
}

mysql_close();
?>