<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/options.php - accesses options set in MySQL options table
*/
function _setOptions()
{
	_threadNumPerPage();
	_postNumPerPage();
	_getSiteTitle();
	_getSiteDescription();
	_getServerTimeZone();
}

function _threadNumPerPage()
{
	if(!isset($_SESSION['osimo']['options']['threadNumPerPage']))
	{
		$query = "SELECT value FROM config WHERE name='thread_num_per_page' LIMIT 1";
		$result = mysql_query($query);
		if($result&&mysql_num_rows($result)>0)
		{
			$_SESSION['osimo']['options']['threadNumPerPage'] = reset(mysql_fetch_row($result));
		}
		else
		{
			$_SESSION['osimo']['options']['threadNumPerPage'] = 10;
		}
	}
	
	return $_SESSION['osimo']['options']['threadNumPerPage'];
}

function _postNumPerPage()
{
	if(!isset($_SESSION['osimo']['options']['postNumPerPage']))
	{
		$query = "SELECT value FROM config WHERE name='post_num_per_page' LIMIT 1";
		$result = mysql_query($query);
		if($result&&mysql_num_rows($result)>0)
		{
			$_SESSION['osimo']['options']['postNumPerPage'] = reset(mysql_fetch_row($result));
		}
		else
		{
			$_SESSION['osimo']['options']['postNumPerPage'] = 10;
		}
	}
	
	return $_SESSION['osimo']['options']['postNumPerPage'];
}

function _getSiteTitle()
{
	if(!isset($_SESSION['osimo']['options']['siteTitle']))
	{
		$query = "SELECT value FROM config WHERE name='site_title' LIMIT 1";
		$result = mysql_query($query);
		
		if($result&&mysql_num_rows($result)>0)
		{
			$_SESSION['osimo']['options']['siteTitle'] = reset(mysql_fetch_row($result));
		}
		else
		{
			$_SESSION['osimo']['options']['siteTitle'] = "Osimo Forum System";
		}
	}
	
	return $_SESSION['osimo']['options']['siteTitle'];
}

function _getSiteDescription()
{
	if(!isset($_SESSION['osimo']['options']['siteDescription']))
	{
		$query = "SELECT value FROM config WHERE name='site_description' LIMIT 1";
		$result = mysql_query($query);
		
		if($result&&mysql_num_rows($result)>0)
		{
			$_SESSION['osimo']['options']['siteDescription'] = reset(mysql_fetch_row($result));
		}
		else
		{
			$_SESSION['osimo']['options']['siteDescription'] = "Powered by Osimo";
		}
	}
	
	return $_SESSION['osimo']['options']['siteDescription'];
}

function _getServerTimeZone()
{
	if(!isset($_SESSION['osimo']['options']['serverTimeZone']))
	{
		$query = "SELECT value FROM config WHERE name='server_time_zone' LIMIT 1";
		$result = mysql_query($query);
		
		if($result&&mysql_num_rows($result)>0)
		{
			$_SESSION['osimo']['options']['serverTimeZone'] = floatval(reset(mysql_fetch_row($result)));
		}
		else
		{
			$_SESSION['osimo']['options']['serverTimeZone'] = 0.0;
		}
	}
	
	return $_SESSION['osimo']['options']['serverTimeZone'];
}
?>