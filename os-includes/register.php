<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/register.php - registers a user for Osimo
*/

session_start();

$username = $_POST['osimo_username'];
$password = $_POST['osimo_password1'];
$email = $_POST['osimo_email1'];
//$spambot = $_POST['osimo_spambot'];

/* PHP Form Validation */
if(!isset($_POST['osimo_username'])||!isset($_POST['osimo_password1'])||!isset($_POST['osimo_email1']))
{ 
	header('Location: ../register.php?register=missingdata'); exit;
}
if(isset($_POST['osimo_password2'])&&$_POST['osimo_password1']!=$_POST['osimo_password2'])
{
	header('Location: ../register.php?register=passmismatch'); exit;
}
if(isset($_POST['osimo_email2'])&&$_POST['osimo_email1']!=$_POST['osimo_email2'])
{
	header('Location: ../register.php?register=emailmismatch'); exit;
}
if(!preg_match("/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{2,4}$/", $email))
{
	header('Location: ../register.php?register=invalidemail');
}
if(strlen($username)<3||strlen($username)>24||preg_match('/[^\w]/', $username))
{
	header('Location: ../register.php?register=usernameinvalid'); exit;
}
/*if(strlen($username)!=$spambot)
{
	header('Location: ../register.php?status=spambotfail'); exit;
}*/


include_once('dbconnect.php');
include_once('paths.php');
include_once('osimo.php');
include_once('mail.php');
$osimo = new Osimo();

$username = secureContent($username);
$password = sha1(secureContent($password));
$email = secureContent($email);
$ipaddress = $_SERVER['REMOTE_ADDR'];
$time_joined = time();

# Protect Against Username & Email Duplicates
$query = "SELECT id FROM users WHERE username='$username' LIMIT 1";
$query2 = "SELECT id FROM users WHERE email='$email' LIMIT 1";
$result = mysql_query($query);
$result2 = mysql_query($query);

if(mysql_num_rows($result) > 0)
{
	$osimo->writeToSysLog('user-register-fail',-1,"unknown user at ".$_SERVER['REMOTE_ADDR']." attempted to register with an already existing username");
	header('Location: ../register.php?register=usertaken'); exit;
}
elseif(mysql_num_rows($result2) > 0)
{
	$osimo->writeToSysLog('user-register-fail',-1,"unknown user at ".$_SERVER['REMOTE_ADDR']." attempted to register with an already existing email address");
	header('Location: ../register.php?register=emailtaken'); exit;
}


$query = "INSERT INTO users (username, username_clean, password, email, time_joined, ip_address,group_list) VALUES ('$username','$username','$password','$email','$time_joined','$ipaddress','1')";
$result = mysql_query($query);

if($result)
{
	/* User successfully registered */
	$userID = mysql_insert_id();

	/* Send welcome email to user */
	$title = "Welcome to Osimo!";
	$content = "<h4>Welcome to Osimo on ".processDomain($_SERVER['HTTP_HOST'])."</h4>";
	$content .= "<p>Your username is '$username' and your password is '".$_POST['osimo_password1']."'</p>";
	$content .= "<p>Thanks for registering with us!</p>";
	sendMail($title,$content,$username,$email,'Osimo Registration',"OsimoRegistration@".processDomain($_SERVER['HTTP_HOST']));	
	
	/* Update statistics */
	$today = $osimo->getTodayTimestamp();
	$stat1 = "SELECT COUNT(*) FROM stats WHERE date='$today' AND type='newuser' LIMIT 1";
	$result1 = mysql_query($stat1);
	if($result1)
	{
		if(reset(mysql_fetch_row($result1))==0)
		{
			$insert = "INSERT INTO stats (forumID,date,type,count) VALUES ('0','$today','newuser','1')";
			$stat2 = mysql_query($insert);
		}
		else
		{
			$update = "UPDATE stats SET count=count+1 WHERE date='$today' AND type='newuser' LIMIT 1";
			$stat2 = mysql_query($update);
		}
	}
	
	$osimo->writeToSysLog('user-register',$userID,$username." is registered");
	header('Location: ../login.php?register=success');
}
else
{
	header('Location: ../register.php?register=fail');
}
?>