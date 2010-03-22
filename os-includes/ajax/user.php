<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/ajax/user.php - ajax backend for user modification
*/
session_start();
include_once('../dbconnect.php'); //connects to database
include_once('../paths.php');
include_once('../osimo.php');
$osimo = new Osimo(); //makes magic happen

/* User profile related ajax backend */
if(isset($_POST['section'])&&$_POST['section']=='info')
{
	$args['birthday_day'] = $_POST['birthday_day'];
	$args['birthday_month'] = $_POST['birthday_month'];
	$args['birthday_year'] = $_POST['birthday_year'];
	$args['field_about'] = $_POST['field_about'];
	$args['field_interests'] = $_POST['field_interests'];
	$args['field_sex'] = $_POST['field_sex'];
	$args['field_website'] = $_POST['field_website'];
	updateProfile('info',$args);
}
if(isset($_POST['section'])&&$_POST['section']=='contact')
{
	$args['field_aim'] = $_POST['field_aim'];
	$args['field_jabber'] = $_POST['field_jabber'];
	$args['field_msn'] = $_POST['field_msn'];
	$args['field_yim'] = $_POST['field_yim'];
	$args['field_icq'] = $_POST['field_icq'];
	updateProfile('contact',$args);
}
if(isset($_POST['section'])&&$_POST['section']=='bio')
{
	$args['field_biography'] = $_POST['field_biography'];
	updateProfile('bio',$args);
}
if(isset($_POST['section'])&&$_POST['section']=='sig')
{
	$args['signature'] = $_POST['signature'];
	updateProfile('sig',$args);
}

/* Memberlist related ajax backend */
if(isset($_POST['memberlist'])){ getMemberList($_POST['page'],$_POST['num'],$_POST['sort'],$_POST['sortDir']); }

/* User CP related ajax backend */
if(isset($_POST['updatepersonal']))
{
	if(isset($_POST['curPassword']))
	{
		$pwdChg = changePassword($_POST['curPassword'],$_POST['newPassword'],$_POST['newPassword2']);
		if($pwdChg)
		{
			updateUserSettings($_POST['displayName'],$_POST['email'],$_POST['timeZone']);
		}
	}
	else
	{
		updateUserSettings($_POST['displayName'],$_POST['email'],$_POST['timeZone']);
	}
}

/* User warning */
if(isset($_POST['warnuser']))
{
	warnUser($_POST['warnuser'],$_POST['warnpost']);
}

function updateUserSettings($displayName,$email,$timeZone)
{
	global $osimo;
	$user = $osimo->getLoggedInUser();
	if($user==false){ echo "0"; exit; }
	
	$displayName = secureContent($displayName);
	$email = secureContent($email);
	$timeZone = secureContent($timeZone);
	if($displayName==''||$email==''||$timeZone=='')
	{
		echo "0"; exit;
	}
	
	$query = "UPDATE users SET username_clean='$displayName',email='$email',time_zone='$timeZone' WHERE id='{$user['ID']}' LIMIT 1";
	$result = mysql_query($query);
	
	if($result)
	{
		$_SESSION['user']['display_name'] = $displayName;
    	$_SESSION['user']['email'] = $email;
    	$_SESSION['user']['time_zone'] = $timeZone;
		echo "1";
	}
	else
	{
		echo "0";
	}
}

function changePassword($curPassword, $newPassword, $newPassword2)
{
	global $osimo;
	
	$user = $osimo->getLoggedInUser();
	if($user==false){ echo "0"; return false; }
	
	$curPassword = sha1(secureContent($curPassword));
	$newPassword = sha1(secureContent($newPassword));
	$newPassword2 = sha1(secureContent($newPassword2));
	$query = "SELECT password FROM users WHERE id='{$user['ID']}' LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		$curPassword2 = reset(mysql_fetch_row($result));
		if($curPassword==$curPassword2)
		{
			if($newPassword==$newPassword2)
			{
				$query = "UPDATE users SET password='$newPassword' WHERE id='{$user['ID']}' LIMIT 1";
				$result = mysql_query($query);
				if($result)
				{
					echo "1";
					return true;
				}
				else
				{
					echo "0";
					return false;
				}
			}
			else
			{
				echo "passmismatch";
				return false;
			}
		}
		else
		{
			echo "passincorrect";
			return false;
		}
	}
	else
	{
		echo "0";
		return false;
	}
}

function updateProfile($section,$_args)
{
	global $osimo;
	$section = secureContent($section);
	$user = $osimo->getLoggedInUser();
	$userID = $user['ID'];
	
	/* First lets escape everything that was entered */
	foreach($_args as $key => $value)
	{
		$args[$key] = htmlspecialchars(secureContent(stripslashes($value)));
	}
	
	if($section=='info')
	{
		/* Retrieve the UNIX timestamp for the birthday */
		$birthday = adodb_mktime(12,0,0,$args['birthday_month'],$args['birthday_day'],$args['birthday_year']);
		$age = date('Y') - $args['birthday_year'];
		
		$args['birthday_month'] = false;
		$args['birthday_day'] = false;
		$args['birthday_year'] = false;
		
		$args = array_filter($args);
		
		$query = "UPDATE users SET birthday='$birthday',field_age='$age'";
		foreach($args as $key => $value)
		{
			$query .= ",$key='$value'";
		}
	}
	
	if($section=='contact')
	{
		$query = "UPDATE users SET ";
		$first=true;
		foreach($args as $key => $value)
		{
			if($first)
			{
				$query .= "$key='$value'";
				$first = false;
			}
			else
			{
				$query .= ",$key='$value'";
			}
		}
	}
	
	if($section=='bio')
	{
		$query = "UPDATE users SET field_biography='{$args['field_biography']}'";
	}
	
	if($section=='sig')
	{
		$query = "UPDATE users SET signature='{$args['signature']}'";
	}
	
	$query .= " WHERE id='$userID' LIMIT 1";
	$result = mysql_query($query);
	
	if($result)
	{
		$osimo->writeToSysLog('user-profile-edit',$user['ID'],$user['name']." edited their profile");
		echo $osimo->getUserSignature($userID,true,true);
	}
	else
	{
		echo "0";
	}
}

function getMemberList($page,$rows = 15,$sort='id',$sortDir='ASC')
{
	global $osimo;
	
	$memberlist = $osimo->getMemberList($page,$rows,$sort,$sortDir);
	$query = "SELECT COUNT(*) FROM users";
	$result = mysql_query($query);
	if($result){ $numMembers = reset(mysql_fetch_row($result)); }
	?>
	<div id="memberlist-wrap">
	<p><?php echo $numMembers; ?> Members | Page <span id="osimo_memberlist-curpage"><?php echo $page; ?></span> of <span id="osimo_memberlist-totpage"><?php echo $osimo->getPagination('table=users', 'num=15'); ?></span></p>
	<table cellpadding='4' cellspacing='0' id="memberlist">
		<tr style="font-weight: bold; cursor:pointer;">
			<td onclick="getMemberList(-1,-1,'id',-2)">ID</td>
			<td onclick="getMemberList(-1,-1,'username',-2)">Username</td>
			<td>Status</td>
			<td onclick="getMemberList(-1,-1,'birthday',-2)">Birthday</td>
			<td onclick="getMemberList(-1,-1,'rank_level',-2)">Rank</td>
			<td onclick="getMemberList(-1,-1,'posts',-2)">Posts</td>
		</tr>
		
	<?php
	foreach($memberlist as $member)
	{
		echo "
		<tr>
    		<td>" . $member['id'] . "</td>
    		<td>" . $member['username'] . "</td>
   		 	<td>" . $member['status'] . "</td>
   			<td>" . $member['birthday'] . "</td>
    		<td>" . $member['rank'] . "</td>
    		<td>" . $member['posts'] . "</td>
		</tr>";
	}
	echo "</table>";
	?>
	<ul class="osimo_memberlist-controls">
		<li onclick="getMemberList('first',-1,-1,-1)">First</li>
		<li onclick="getMemberList('prev',-1,-1,-1)">Previous</li>
		<li onclick="getMemberList('next',-1,-1,-1)">Next</li>
		<li onclick="getMemberList('last',-1,-1,-1)">Last</li>
	</ul>
	<?php
}

function resetPassword($step,$type,$data)
{
	global $osimo;
	$data = secureContent($data);
	/* User entered username */
	if($step==1&&$type==1)
	{
		/* First we need to make sure the user exists */
		$query = "SELECT COUNT(*) FROM users WHERE username='$data' LIMIT 1";
		$result = mysql_query($query);
		if(reset(mysql_fetch_row($result))==1)
		{
			/* User exists, proceed with reset */
			$code = sha1(time()+rand(0,9999));
			$query2 = "UPDATE users SET reset_code='$code' WHERE username='$data' LIMIT 1";
			$result2 = mysql_query($query2);
			if($result2)
			{
				$query3 = "SELECT email FROM users WHERE username='$data' LIMIT 1";
				$result3 = mysql_query($query3);
				if($result3){ $email = reset(mysql_fetch_row($result3)); $username = $data; }
			}
		}
		else
		{
			echo "0";
		}
	}
	/* User entered email address */
	elseif($step==1&&$type==2)
	{
	    /* First, check to make sure email address is in database */
	    $query = "SELECT COUNT(*) FROM users WHERE email='$data' LIMIT 1";
	    $result = mysql_query($query);
	    if(reset(mysql_fetch_row($result))==1)
	    {
	    	/* Email exists, proceed with reset */
	    	$code = sha1(time()+rand(0,9999));
	    	$query2 = "UPDATE users SET reset_code='$code' WHERE email='$data' LIMIT 1";
	    	$result2 = mysql_query($query2);
	    	if($result2)
	    	{
	    		$query3 = "SELECT username FROM users WHERE email='$data' LIMIT 1";
	    		$result3 = mysql_query($query3);
	    		if($result3){ $username = reset(mysql_fetch_row($result3)); $email = $data; }
	    	}
	    }
	}
	if($step==1)
	{
		if($result3)
		{
		    include_once(ABS_INCLUDES.'mail.php');
		    /* Send welcome email to user */
		    $title = "Osimo Password Reset";
		    $content = "<h4>Osimo Password Reset</h4>";
		    $content .= "<p>Someone (probably you) has requested a password reset for the username $username on ".processDomain($_SERVER['HTTP_HOST'])."</p>";
		    $content .= "<p>If you did not make this request, then simply ignore this email and your account will not be touched.</p>";
		    $content .= "<p>To reset your password, enter this code into the Reset Code field: $code</p>";
		    sendMail($title,$content,$username,$email,'Osimo Registration',"OsimoRegistration@".processDomain($_SERVER['HTTP_HOST']));
		    
		    getForgotPasswordBox(1);
		}
	}
	
	if($step==2)
	{
		/* User is resetting password, need code validation check */
		if($data['pass1']!=$data['pass2'])
		{
			echo "<p>The passwords entered do not match!</p>"; getForgotPasswordBox(1); exit;
		}
		
		$query = "SELECT id FROM users WHERE reset_code='{$data['code']}' LIMIT 1";
		$result = mysql_query($query);
		if($result&&mysql_num_rows($result)>0)
		{
			while(list($id)=mysql_fetch_row($result))
			{
				/* Code entered is correct, continue */
				$password = sha1($data['pass1']);
				$query2 = "UPDATE users SET password='$password' WHERE id='$id' LIMIT 1";
				$result2 = mysql_query($query2);
				if($result2)
				{
					$osimo->writeToSysLog('password-reset',$user['ID'],$user['name']." has reset their password");
					getForgotPasswordBox(2);
				}
			}
		}
		else
		{
			echo "<p>The reset code entered is not correct!</p>"; getForgotPasswordBox(1); exit;
		}
	}
}

function warnUser($userID,$postID)
{
	global $osimo;
	
	if(!$osimo->userIsAdmin()&&!$osimo->userIsModerator()){ echo "0"; exit; }
	
	$result = $osimo->createWarning($userID,$postID);
	
	if($result){ echo "1"; }
	else{ echo "0"; }
}
?>