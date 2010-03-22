<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	logout.php - logs a user out of Osimo
*/
session_start();
include_once('os-includes/dbconnect.php'); //connects to database
include_once('os-includes/paths.php');
include_once('os-includes/osimo.php');
$osimo = new Osimo(); //makes magic happen

$user = $osimo->getLoggedInUser();
if($user!=false)
{
	/* Need to update database to make sure user becomes offline */
	$query = "UPDATE users SET last_page_type='logoff',last_page_id='0' WHERE id='{$user['ID']}' LIMIT 1";
	$result = mysql_query($query);
}

session_destroy();
setcookie("osimo[user]", '', time()-3600,'/');
setcookie("osimo[pass]", '', time()-3600,'/');
setcookie("osimo[data]", '', time()-3600,'/');

$user = $osimo->getLoggedInUser();
if($user)
{
	$osimo->writeToSysLog('user-logout',$user['ID'],$user['name']." logged out");
}

				
header("location: index.php?logout=true");

mysql_close();
?>