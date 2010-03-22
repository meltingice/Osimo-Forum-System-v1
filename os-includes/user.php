<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/user.php - some useful functions relating to users
*	Functions should be accessed through the Osimo class
*/

function _checkForLogin()
{
	if(!isset($_SESSION['user']))
	{
		/* Cookies are set but user's session has ended */
		if(isset($_COOKIE['osimo']))
		{
			$password = $_COOKIE['osimo']['pass'];
			$query = "SELECT id, username, username_clean, email, password, time_last_visit,is_admin,time_zone FROM users WHERE id='{$_COOKIE['osimo']['user']}' AND password='$password' LIMIT 1";
			$result = mysql_query($query);
			if($result&&mysql_num_rows($result)>0)
			{
				while(list($id,$username,$username_clean,$email,$password,$time_last_visit,$is_admin,$time_zone)=mysql_fetch_row($result))
    			{
    				if(sha1($id.$username.$password)==$_COOKIE['osimo']['data'])
    				{
    					/* Welcome back to Osimo, time to set some useful session variables */
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
    					
    					/* Update users table with current time */
    					$query2 = "UPDATE users SET time_last_visit='$lastlogin',ip_address='".$_SERVER['REMOTE_ADDR']."' WHERE id='$id' LIMIT 1";
    					$result2 = mysql_query($query2);
    					
    					/* Renew the cookie */
		    			setcookie("osimo[user]", $id, time()+60*60*24*365,'/');
    					setcookie("osimo[pass]",$password, time()+60*60*24*365,'/');
    					setcookie("osimo[data]",sha1($id.$username.$password), time()+60*60*24*365,'/');
    				}
    			}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}

function _getUserProfileInfo($id)
{
	include_once(ABS_INCLUDES.'bbcode/bbparser.php');
	$id = secureContent($id);
	
	$query = "SELECT username,birthday,posts,field_age,field_sex,field_location,field_aim,field_jabber,field_msn,field_yim,field_icq,field_website,field_about,field_interests,field_biography,time_joined,time_last_visit,time_last_post,last_page,time_zone,time_format FROM users WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		$user = mysql_fetch_array($result, MYSQL_ASSOC);
		$user['field_biography_unformatted'] = $user['field_biography'];
		$user['field_biography'] = bb2html($user['field_biography']);
		$user['field_website_unformatted'] = $user['field_website'];
		$user['field_website'] = "<a href=\"{$user['field_website']}\">{$user['field_website']}</a>";
		return $user;
	}
	else
	{
		return false;
	}
}

function _getUserSignature($id,$formatted,$forceDB)
{
	global $osimo;
	include_once(ABS_INCLUDES.'bbcode/bbparser.php');
	$id = secureContent($id);
	
	if($forceDB==false)
	{
		if(isset($_SESSION['sigs'])&&array_key_exists($id,$_SESSION['sigs'])&&$_SESSION['sigs'][$id]!=false)
		{
			if($formatted){ return bb2html(htmlspecialchars($_SESSION['sigs'][$id])); }
			else{ return $_SESSION['sigs'][$id]; }
		}
	}
	
	$query = "SELECT signature FROM users WHERE id='$id' LIMIT 1";
	$result = $osimo->cache->sqlquery($query);
	if($result)
	{
		$sig = $result[0]['signature'];
		if($formatted)
		{
			return bb2html($sig);
		}
		else
		{
			return $sig;
		}
	}
	else
	{
		return false;
	}
}

function _getUserAvatar($id,$formatted)
{
	global $osimo;
	if($id==-1)
	{
		$user = $osimo->getLoggedInUser();
		$id = $user['ID'];
	}
	
	$abs_avapath = ABS_AVATAR_FOLDER.$id;
	if(file_exists($abs_avapath.'.jpg')){ $ext = '.jpg'; }
	elseif(file_exists($abs_avapath.'.gif')){ $ext = '.gif'; }
	elseif(file_exists($abs_avapath.'.png')){ $ext = '.png'; }
	else{ $ext = false; }
	
	if($ext)
	{
		$data = AVATAR_FOLDER.$id.$ext;
	}
	else
	{
		if(file_exists(ABS_THEMEPATH.'noavatar.jpg')){ $data = THEMEPATH_URL.$path."noavatar.jpg"; }
		elseif(file_exists(ABS_THEMEPATH.'noavatar.gif')){ $data = THEMEPATH_URL.$path."noavatar.gif"; }
		elseif(file_exists(ABS_THEMEPATH.'noavatar.png')){ $data = THEMEPATH_URL.$path."noavatar.png"; }
		else{ $data = AVATAR_FOLDER.$path.'noavatar.jpg'; }
	}
	
	if($formatted)
	{
		return "<img src=\"$data\" alt=\"avatar_$id\" />";
	}
	else
	{
		return $data;
	}
}

function _userIDtoName($userID)
{
	global $osimo;
	if($userID==-1){ return "Guest"; }
	else
	{
		$userID = secureContent($userID);
		$query = "SELECT username FROM users WHERE id='$userID' LIMIT 1";
		$result = $osimo->cache->sqlquery($query);
		if($result)
		{
			return $result[0]['username'];
		}
	}
	
	return false;
}

function _getUserInfo($id,$data)
{
	$id = secureContent($id);
	$data = secureContent($data);
	
	$disallowed = array('password','signature','group_list');

	if(array_search($data,$disallowed)){ return false; }
	
	$query = "SELECT ";
	if(is_array($data))
	{
		for($i=0;$i<count($data);$i++)
		{
			$query .= $data[$i];
			if($i!=count($data)-1)
			{
				$query .= ",";
			}
		}
	}
	else
	{
		$query .= $data;
	}
	$query .= " FROM users WHERE id='$id'";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result))
	{
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}
	else
	{
		return false;
	}
}

function _userNametoID($username)
{
	global $osimo;
	if($username==''){ return false; }
	else
	{
		$username = secureContent($username);
		$query = "SELECT id FROM users WHERE username='$username' LIMIT 1";
		$result = $osimo->cache->sqlquery($query);
		if($result)
		{
			return $result[0]['id'];
		}
	}
}

function _userIsPostOwner($id)
{
	global $osimo;
	$id = secureContent($id);
	$user = $osimo->getLoggedInUser();
	
	$query = "SELECT COUNT(*) FROM post WHERE id='$id' AND poster_id='{$user['ID']}' LIMIT 1";
	$result = $osimo->cache->sqlquery($query);
	
	if($result){ return reset($result[0]); }
	else{ return false; }
}

function _getUserLocalTime($id)
{
	global $osimo;
	$id = secureContent($id);
	 
	$query = "SELECT time_zone FROM users WHERE id='$id' LIMIT 1";
	$result = $osimo->cache->sqlquery($query);
	
	if($result)
	{
		$offset = $result[0]['time_zone']*60*60;
		
		$gmt_str = adodb_gmdate("M d Y H:i:s",time());
		$gmt = strtotime($gmt_str);
		
		if(adodb_date('I')==1){ $gmt = $gmt + 3600; }
	   
		$time = $gmt + $offset;
	   
		return $time;
	}
	else
	{
	   return time();
	}
}

function _getNewestUser()
{
	$query = "SELECT id,username,time_joined FROM users ORDER BY time_joined DESC LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		while(list($id,$username,$time_joined)=mysql_fetch_row($result))
		{
			$user['id'] = $id;
			$user['name'] = $username;
			$user['time_joined'] = $time_joined;
		}
		
		return $user;
	}
	else
	{
		return false;
	}
}

function _getOnlineUsers($type,$id)
{
	global $osimo;
	
	$time = time();
	$offset = $time % 300;
	$interval = ($time - $offset) - 300;
	
	$query = "SELECT id,username FROM users WHERE time_last_visit >= $interval";
	if($type!=-1&&$id!=-1)
	{
		$type = secureContent($type);
		$id = secureContent($id);
		
		$query .= " AND last_page_type='$type' AND last_page_id='$id'";
	}
	else
	{
		$query .= " AND last_page_type!='logoff'";
	}
	$query .= " ORDER BY is_admin DESC,is_staff DESC,is_global_mod DESC,username ASC";
	
	$result = $osimo->cache->sqlquery($query);
	
	if($result)
	{
		foreach($result as $user){
			$users[$user['id']] = $user['username'];
		}

		return $users;
	}
	else
	{
		return false;
	}
}

function _isUserOnline($id)
{
	global $osimo;
	
	$time = time();
	$offset = $time % 300;
	$interval = $time - $offset - 300;
	
	$query = "SELECT COUNT(*) FROM users WHERE id='$id' AND time_last_visit >=$interval";
	$result = $osimo->cache->sqlquery($query);
	
	return reset(reset($result));
}

function _setUserOnline()
{
	global $osimo;
	
	$user = $osimo->getLoggedInUser();
	if($user)
	{
		$query = "UPDATE users SET time_last_visit='".time()."',last_page_type='other',last_page_id='0' WHERE id='{$user['ID']}' LIMIT 1";
		$result = mysql_query($query);
		
		if($result){ return true; }
		else{ return false; }
	}
	
	return true;
}

/* Start Warnings Functions */

function _createWarning($user_id, $post_id = 0)
{
	$user_id = secureContent($user_id);
	$post_id = secureContent($post_id);
	$query = "INSERT INTO warning (user_id,post_id,warning_time) VALUES ('$user_id','$post_id','".time()."')";
	$result = mysql_query($query);
	
	if($result){ return true; }
	else{ return false; }
}

function _getUserWarnings($user_id)
{
	$user_id = secureContent($user_id);
	$data = array();
	$data['num_warnings']=0;
	$data['active_warnings']=0;
	$data['warn_posts'] = array();
	$data['posts'] = array();

	$query = "SELECT post_id,warning_time FROM warning WHERE user_id='$user_id'";
	$result = mysql_query($query);
	
	if($result)
	{
		while(list($post_id,$warning_time)=mysql_fetch_row($result))
		{
			$data['num_warnings']++;
			$data['posts'][$post_id] = $warning_time;
			$data['warn_posts'][] = $post_id;
			
			if((time()-$warning_time)<604800)
			{
				$data['active_warnings']++;
			}
		}
	}
	
	return $data;
}

function _allowLogin($user_id)
{
	$query = "SELECT id, user_id, log_attempts, warning_time FROM warning WHERE user_id='" . $user_id . "' AND warning_time >= '" . (time() - 900) . "'";
	$result  = mysql_query($query);
	
	if($fetch_warning = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if($fetch_warning['log_attempts'] >= 5)
		{
			return 0;
		}
	}
	
	return 1;
}

/* End Warnings Functions */

function _getUsernameColor($userID)
{
	global $osimo;
	$userID = secureContent($userID);
	$result = $osimo->cache->sqlquery("SELECT username_color FROM users WHERE id='$userID' LIMIT 1");
	
	if($result)
	{
		$color = "#".$result[0]['username_color'];
		return $color;
	}
	
	return false;
}

function _numUserReports(){
	$query = "SELECT COUNT(distinct post_id) FROM reports WHERE ignore_report='0'";
	$result = mysql_query($query);
	if($result && mysql_num_rows($result)>0){
		return reset(mysql_fetch_row($result));
	}
	
	return 0;
}
?>