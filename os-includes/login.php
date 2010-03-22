<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/login.php - logs a user into Osimo
*/

session_start();

if(!isset($_POST['osimo_username'])){ header('Location: ../index.php'); exit; }

include_once('dbconnect.php'); //connects to database
include_once('paths.php');
include_once('osimo.php');
include_once('bans.php');
$osimo = new Osimo(); //makes magic happen

if(!allowLogin(false,$_SERVER['REMOTE_ADDR'])){
	header('Location: index.php'); exit;
}

$_username = secureContent($_POST['osimo_username']);
$_password = sha1(secureContent($_POST['osimo_password']));


/* Check to make sure user/IP isn't banned first */
/*
	$ip = $_SERVER['REMOTE_ADDR'];
$banQuery1 = "SELECT COUNT(*) FROM banlist WHERE username='$username'";
$banResult1 = mysql_query($banQuery1);
$banCheck1 = reset(mysql_fetch_row($banResult1));
if(!$banCheck1) // ok the username isn't banned, check for the IP
{
	$banQuery2 = "SELECT COUNT(*) FROM banlist WHERE username='' AND ipaddress='$ip'";
	$banResult2 = mysql_query($banQuery2);
	if(reset(mysql_fetch_row($banResult2)))
	{
		header('Location: ../index.php?login=ipbanned'); exit;
	}
}
else
{
	header('Location: ../index.php?login=userbanned'); exit;
}
*/

/* Made it through the ban checks, continue with login */
$query = "SELECT id, username, username_clean, email, password, time_last_visit,is_admin,time_zone FROM users WHERE username='$_username' LIMIT 1";
$result = mysql_query($query);

if($result&&mysql_num_rows($result)>0)
{
    while(list($id,$username,$username_clean,$email,$password,$time_last_visit,$is_admin,$time_zone)=mysql_fetch_row($result))
    {
    	if($_password==$password)
    	{
    		/* Reset user permissions */
    		session_unset();
    		
    		/* Welcome to Osimo, time to set some useful session variables */
    		$_SESSION['user']['ID'] = $id;
    		$_SESSION['user']['name'] = $username;
    		$_SESSION['user']['display_name'] = $username_clean;
    		$_SESSION['user']['email'] = $email;
    		$_SESSION['user']['last_login'] = $time_last_visit;
    		$_SESSION['user']['time_zone'] = $time_zone;
    		$lastlogin = time();
    		
    		if($is_admin)
    		{
    			$_SESSION['admin']['ID'] = $id;
    			$_SESSION['admin']['name'] = $username;
    			$_SESSION['admin']['display_name'] = $username_clean;
    		}
    		
    		/* Store Cookie */
    		if($_POST['osimo_rememberme']=='on')
    		{
    			setcookie("osimo[user]", $id, time()+60*60*24*365,'/');
	    		setcookie("osimo[pass]",$password, time()+60*60*24*365,'/');
	    		setcookie("osimo[data]",sha1($id.$username.$password), time()+60*60*24*365,'/');
    		}
    		
    		/* Update users table with current time */
    		$query = "UPDATE users SET time_last_visit='$lastlogin',ip_address='".$_SERVER['REMOTE_ADDR']."' WHERE id='$id' LIMIT 1";
    		$result = mysql_query($query);
    		
			$osimo->writeToSysLog('user-login',$id,$username." logged in");
			
    		header('Location: ../index.php?login=success'); exit;
    	}
    	else
    	{
    		/* Username found, password incorrect */
			$osimo->writeToSysLog('user-login-fail',$id,$username." at ".$_SERVER['REMOTE_ADDR']." attempted to login with an incorrect password");
			
	    	header('Location: ../login.php?login=passfail'); exit;
    	}
    }   
}
else
{
    /* Username not found */
	$osimo->writeToSysLog('user-login-fail',-1,"unknown person at ".$_SERVER['REMOTE_ADDR']." attempted to login with an unknown username");
    header('Location: ../login.php?login=userfail');
}

mysql_close();
?>