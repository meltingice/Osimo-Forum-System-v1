<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-admin/admin.php - this is where the admin magic happens
*	Use this class to access most Osimo admin functions
*/

class OsimoAdmin
{
	function OsimoAdmin()
	{
		include_once(ABS_INCLUDES.'security.php');
		include_once(ABS_INCLUDES.'utilities.php');
		include_once(ABS_INCLUDES.'adodb-time.inc.php');
	}
	
	function getForumList()
	{
		include_once(ABS_ADMIN_INCLUDES.'forum.php');
		if(func_num_args()>0){ $args = $this->processArgs(func_get_args()); }
		else{ $args = null; }
		
		if(array_key_exists('all',$args))
		{
			$all = $args['all'];
			$args['all'] = false;
		}
		else
		{
			$all = false;
		}
		
		$args = array_filter($args);
		
		return admin_getForumList($all,$args);
	}
	
	function getForumNames()
	{
		include_once(ABS_ADMIN_INCLUDES.'forum.php');
		
		return admin_getForumNames();
	}
	
	function getCategoryList()
	{
		include_once(ABS_ADMIN_INCLUDES.'forum.php');
		
		return admin_getCategoryList();
	}
	
	function getUserList($page,$num,$sort,$dir)
	{
		include_once(ABS_ADMIN_INCLUDES.'user.php');
		
		return admin_getUserList($page,$num,$sort,$dir);
	}
	
	function getAvailableThemes()
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getAvailableThemes();
	}
	
	function getThemeFilelist($name)
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getThemeFilelist($name);
	}
	
	function getAvailableSmilies()
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getAvailableSmilies();
	}
	
	function getSmileyPreview($name)
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getSmileyPreview($name);
	}
	
	function getActiveSmilies()
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getActiveSmilies();
	}
	
	function getActiveSmileyName()
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getActiveSmileyName();
	}
	
	/* Statistics Retrieval */
	function getTodayStats($type)
	{
		include_once(ABS_ADMIN_INCLUDES.'stats.php');
		
		return admin_getTodayStats($type);
	}
	
	function getTopUsers($num)
	{
		include_once(ABS_ADMIN_INCLUDES.'user.php');
		
		return admin_getTopUsers($num);
	}
	
	function getForumStats($id,$startDate,$endDate,$type)
	{
		include_once(ABS_ADMIN_INCLUDES.'stats.php');
		
		return admin_getForumStats($id,$startDate,$endDate,$type);
	}
	
	function outputTodayStats()
	{
		include_once(ABS_ADMIN_INCLUDES.'stats.php');
		
		admin_outputTodayStats();
	}
	
	/* Theme Management */
	function getThemePreview($theme)
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getThemePreview($theme);
	}
	
	function getThemeInfo($theme)
	{
		include_once(ABS_ADMIN_INCLUDES.'theme.php');
		
		return admin_getThemeInfo($theme);
	}
	
	function getSiteInfo()
	{
		include_once(ABS_ADMIN_INCLUDES.'site.php');
		
		return admin_getSiteInfo();
	}
	/* End theme management */
	
	function readFromSysLog($startTime, $endTime, $filter=false)
	{
		include_once(ABS_ADMIN_INCLUDES.'ajax/statistics.php');
		
		return admin_readFromSysLog($startTime,$endTime,$filter);
	}
	
	function processArgs($args)
	{
		$argArray = array();
		if(is_array($args)&&count($args)>0)
		{
			foreach($args as $arg)
			{
				$temp = explode('=',$arg);
				$argArray[$temp[0]] = $temp[1];
			}
		}
		else
		{
			$temp = explode('=',$args);
			$argArray[$temp[0]] = $temp[1];
		}
		
		return $argArray;
	}
}
?>