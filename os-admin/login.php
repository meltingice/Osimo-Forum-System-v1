<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/login.php - logs a user into Osimo
*/

session_start();

if(!isset($_POST['osimo_username'])){ header('Location: ../index.php'); exit; }

include("../os-includes/dbconnect.php");
include("../os-includes/security.php");
include("../os-includes/osimo.php");

$username = secureContent($_POST['osimo_username']);
$_password = sha1(secureContent($_POST['osimo_password']));

/* Made it through the ban checks, continue with login */
$query = "SELECT id, username_clean, password, is_admin,is_global_mod FROM users WHERE username='$username' LIMIT 1";
$result = mysql_query($query);

if($result&&mysql_num_rows($result)>0)
{
    while(list($id,$username_clean,$password,$is_admin,$is_global_mod, $time_last_visit)=mysql_fetch_row($result))
    {
    	if($_password==$password)
    	{
    		if($is_admin==1||$is_global_mod==1)
    		{
    			/* Welcome to Osimo, time to set some useful session variables */
    			$_SESSION['admin']['ID'] = $id;
    			$_SESSION['admin']['name'] = $username;
    			$_SESSION['admin']['name_clean'] = $username_clean;
    			$_SESSION['admin']['is_admin'] = $is_admin;
    			
    			if(isset($_POST['return']))
    			{
    				header('Location: '.rawurldecode($_POST['return']));
    			}
    			else
    			{
	    			header('Location: home.php'); exit;
    			}
    		}
    	}
    	else
    	{
    		/* Username found, password incorrect */
	    	header('Location: index.php?login=fail'); exit;
    	}
    }   
}
else
{
    /* Username not found */
    header('Location: index.php?login=userfail');
}

mysql_close();
?>