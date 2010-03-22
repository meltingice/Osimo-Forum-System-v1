<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/utilities.php - useful random functions
*	Most functions should be accessed through the Osimo class
*/

/* 
*	Builds the WHERE clause of a MySQL query when passed an array of arguements
*	returns string
*/
function buildQuery($args)
{
	$query = " WHERE";
	$keys = array_keys($args);
	for($i=0;$i<count($keys);$i++)
	{
		$key = secureContent($keys[$i]);
		$arg = secureContent($args[$keys[$i]]);
		if($i!=0){ $query .= " AND"; }
		
		$query .= " ".$key."='".$arg."'";
	}
		
	return $query;
}

function _getPagination($table,$num,$args)
{
	global $osimo;
	$table = secureContent($table);
	if($num==-1) //retrieve from MySQL table
	{
		if($table!='thread'&&$table!='post'){ return false; }
		$numQuery = "SELECT value FROM config WHERE name='".$table."_num_per_page' LIMIT 1";
		$numResult = $osimo->cache->sqlquery($numQuery,86400);
		if($numResult)
		{
			$num = $numResult[0]['value'];
		}
		else //something weird happened, lets use a default
		{
			$num = 10;
		}
	}
	$num = secureContent($num);
	
	$query = "SELECT COUNT(*) FROM $table";
	if(is_array($args)&&count($args)>0)
	{
		$query .= buildQuery($args);
	}
	
	$result = mysql_query($query);
	$pages = ceil(reset(mysql_fetch_row($result))/$num);
	if($pages==0){ return 1; }
	else{ return $pages; }
}

function _getTodayTimestamp()
{
	$now = time();
	$timeStr = date('d F Y').' 12:00am';
	return strtotime($timeStr);
}

function _floodControl($type)
{
	global $osimo;
	
	$user = $osimo->getLoggedInUser();
	$waitTime = time()-10;
	
	if($type=='post')
	{
		$query = "SELECT time_last_post FROM users WHERE id='{$user['ID']}' LIMIT 1";
		$result = mysql_query($query);
		
		if($result&&mysql_num_rows($result)>0)
		{
			if(reset(mysql_fetch_row($result))<$waitTime)
			{
				return true;
			}
			else
			{
				$osimo->writeToSysLog('flood-control',$user['ID'],$user['name']." attempted to flood a thread");
				return false;
			}
		}
	}
	if($type=='thread')
	{
		$query = "SELECT COUNT(*) FROM thread WHERE original_poster_id='{$user['ID']}' AND original_post_time>'$waitTime'";
		$result = mysql_query($query);
		
		if(reset(mysql_fetch_row($result))>0)
		{
			$osimo->writeToSysLog('flood-control',$user['ID'],$user['name']." attempted to flood a forum");
			return false;
		}
		else
		{
			return true;
		}
	}
	
	return false;
}

function _writeToSysLog($type,$user,$message)
{
	$type = secureContent($type);
	$user = secureContent($user);
	$message = secureContent($message);
	$time = time();
	
	$query = "INSERT INTO syslog (time,type,user,message) VALUES ('$time','$type','$user','$message')";
	$result = mysql_query($query);
	
	if($result){ return true; }
	else{ return false; }
}

function _getUserTime($timestamp)
{
	global $osimo;
	$user = $osimo->getLoggedInUser();
	
	if(isset($_SESSION['osimo']['options']))
	{
		$serverTimeZone = $_SESSION['osimo']['options']['serverTimeZone'];
	}
	else
	{
		$serverTimeZone = 0.0;
	}
	
	if($user==false)
	{
		if(!$timestamp)
		{
			return time();
		}
		else
		{
			return $timestamp;
		}
	}
	else
	{
		if(!$timestamp)
		{
			return time() + (($user['time_zone']-$serverTimeZone)*60*60);
		}
		else
		{
			return $timestamp + (($user['time_zone']-$serverTimeZone)*60*60);
		}
	}
}
?>