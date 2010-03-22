<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/forum.php - functions relating to groups
*	Functions should be accessed through the Osimo class
*/

function _setUserGroups()
{
	if(!isset($_SESSION['osimo']['groups']))
	{
		if(isset($_SESSION['user'])&&$_SESSION['user']['ID']!=-1)
		{
			$groups = _getUserGroups(array("id"=>$_SESSION['user']['ID']));
			if(is_array($groups))
			{
				$i=0;
				foreach($groups as $group)
				{
					$_SESSION['osimo']['groups'][$i]['ID'] = $group['ID'];
					$_SESSION['osimo']['groups'][$i]['name'] = $group['name'];
					$i++;
				}
			}
		}
		else
		{
			$query = "SELECT id,description,username_style,username_color FROM groups WHERE name='Guest' LIMIT 1";
			$result = mysql_query($query);
			if($result&&mysql_num_rows($result)>0)
			{
				while(list($id,$description,$username_style,$username_color)=mysql_fetch_row($result))
				{
					$_SESSION['osimo']['groups'][0]['ID'] = $id;
					$_SESSION['osimo']['groups'][0]['name'] = "Guest";
					$_SESSION['osimo']['groups'][0]['description'] = $description;
					$_SESSION['osimo']['groups'][0]['username_style'] = $username_style;
					$_SESSION['osimo']['groups'][0]['username_color'] = $username_color;
				}
			}
		}
	}
}

function _getUserGroups($args)
{	
	if(!isset($_SESSION['osimo']['groups']))
	{	
		$query = "SELECT group_list FROM users";
		if(is_array($args)&&count($args)>0)
		{
			$query .= buildQuery($args);
		}
		$result = mysql_query($query);
		
		if($result)
		{
			$group_list = reset(mysql_fetch_row($result));
			$temp = explode(",",$group_list);
			
			for($i=0;$i<count($temp);$i++)
			{
				$groups[$i]['ID'] = $temp[$i];
				$query = "SELECT name,description,username_style,username_color FROM groups WHERE id='{$temp[$i]}' LIMIT 1";
				$result = mysql_query($query);
				if($result&&mysql_num_rows($result)>0)
				{
					while(list($name,$description,$username_style,$username_color)=mysql_fetch_row($result))
					{
						$groups[$i]['name'] = $name;
						$groups[$i]['description'] = $description;
						$groups[$i]['username_style'] = $username_style;
						$groups[$i]['username_color'] = $username_color;
					}
				}
			}
			
			return $groups;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return $_SESSION['osimo']['groups'];
	}
}

function _setPermissions()
{
	global $osimo;
	
	/* We only want to do work if we need to :) */
	if(!isset($_SESSION['osimo']['perms']['global']))
	{
		if(isset($_SESSION['user'])){ $userID = $_SESSION['user']['ID']; }
		else{ $userID = -1; }
		
		/* First lets get and set global permissions from the groups table */
		$query = "SELECT 
				perm_can_view_forum,
				perm_can_view_thread,
				perm_can_post_thread,
				perm_can_post_reply,
				perm_can_post_links,
				perm_can_edit,
				perm_can_create_poll,
				perm_can_vote,
				perm_can_send_pm,
				perm_can_receive_pm,
				perm_can_receive_alert,
				perm_can_view_profile,
				perm_can_edit_profile FROM groups WHERE";
				
		if($userID=='-1') //if guest
		{
			 $query .= " name='Guest' LIMIT 1";	
		}
		else
		{
			$groups = _getUserGroups(array("id"=>$userID));
			for($i=0;$i<count($groups);$i++)
			{
				if($i!=0){ $query .= " OR"; }
				$query .= " id='{$groups[$i]['ID']}'";
			}
		}
		
		$result = mysql_query($query);
		
		if($result)
		{
			while(list($perm_can_view_forum,$perm_can_view_thread,$perm_can_post_thread,$perm_can_post_reply,$perm_can_post_links,$perm_can_edit,$perm_can_create_poll,$perm_can_vote,$perm_can_send_pm,$perm_can_receive_pm,$perm_can_receive_alert,$perm_can_view_profile,$perm_can_edit_profile)=mysql_fetch_row($result))
			{
			    $_SESSION['osimo']['perms']['global']['view_forum'] = max($perm_can_view_forum,$_SESSION['osimo']['perms']['global']['view_forum']);
			    $_SESSION['osimo']['perms']['global']['view_thread'] = max($perm_can_view_thread,$_SESSION['osimo']['perms']['global']['view_thread']);
			    $_SESSION['osimo']['perms']['global']['post_thread'] = max($perm_can_post_thread,$_SESSION['osimo']['perms']['global']['post_thread']);
			    $_SESSION['osimo']['perms']['global']['post_reply'] = max($perm_can_post_reply,$_SESSION['osimo']['perms']['global']['post_reply']);
			    $_SESSION['osimo']['perms']['global']['post_links'] = max($perm_can_post_links,$_SESSION['osimo']['perms']['global']['post_links']);
			    $_SESSION['osimo']['perms']['global']['edit'] = max($perm_can_edit,$_SESSION['osimo']['perms']['global']['edit']);
			    $_SESSION['osimo']['perms']['global']['create_poll'] = max($perm_can_create_poll,$_SESSION['osimo']['perms']['global']['create_poll']);
			    $_SESSION['osimo']['perms']['global']['vote'] = max($perm_can_vote,$_SESSION['osimo']['perms']['global']['vote']);
			    $_SESSION['osimo']['perms']['global']['send_pm'] = max($perm_can_send_pm,$_SESSION['osimo']['perms']['global']['send_pm']);
			    $_SESSION['osimo']['perms']['global']['receive_pm'] = max($perm_can_receive_pm,$_SESSION['osimo']['perms']['global']['receive_pm']);
			    $_SESSION['osimo']['perms']['global']['receive_alert'] = max($perm_can_receive_alert,$_SESSION['osimo']['perms']['global']['receive_alert']);
			    $_SESSION['osimo']['perms']['global']['view_profile'] = max($perm_can_view_profile,$_SESSION['osimo']['perms']['global']['view_profile']);
			    $_SESSION['osimo']['perms']['global']['edit_profile'] = max($perm_can_edit_profile,$_SESSION['osimo']['perms']['global']['edit_profile']);
			}
		}
		else
		{
			return false;
		}
		
		/* Lets check and see if the user is an admin or a global moderator */
		if($userID!=-1)
		{
			$query = "SELECT is_admin,is_global_mod FROM users WHERE id='$userID' LIMIT 1";
			$result = mysql_query($query);
			
			if($result)
			{
				while(list($is_admin,$is_global_mod)=mysql_fetch_row($result))
				{
					$_SESSION['osimo']['perms']['global']['admin'] = $is_admin;
					$_SESSION['osimo']['perms']['global']['global_mod'] = $is_global_mod;
				}
			}
		}
		
		/* Now that global permissions are set, we need to set permissions per forum */
		$query2 = "SELECT 
			forum,
			perm_can_moderate_forum,
			perm_can_view_forum,
			perm_can_view_thread,
			perm_can_post_thread,
			perm_can_post_reply,
			perm_can_post_links,
			perm_can_edit,
			perm_can_create_poll,
			perm_can_vote FROM forum_permissions WHERE";
			
		if($userID=='-1') //if guest
		{
			$query3 = "SELECT id FROM groups WHERE name='Guest' LIMIT 1";
			$result3 = mysql_query($query3);
			$guestID = reset(mysql_fetch_row($result3));
			$query2 .= " `group`='$guestID'";	
		}
		else
		{
			for($i=0;$i<count($groups);$i++)
			{
				if($i!=0){ $query2 .= " OR"; }
				$query2 .= " `group`='{$groups[$i]['ID']}'";
			}
		}
		
		$result2 = mysql_query($query2);
		if($result2)
		{
			while(list($forum,$perm_can_moderate_forum,$perm_can_view_forum,$perm_can_view_thread,$perm_can_post_thread,$perm_can_post_reply,$perm_can_post_links,$perm_can_edit,$perm_can_create_poll,$perm_can_vote)=mysql_fetch_row($result2))
			{
				if(!isset($_SESSION['osimo']['perms']['spec'][$forum]['view_forum']))
				{
					$_SESSION['osimo']['perms']['spec'][$forum]['moderate_forum'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['view_forum'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['view_thread'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['post_thread'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['post_reply'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['post_links'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['edit'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['create_poll'] = 0;
					$_SESSION['osimo']['perms']['spec'][$forum]['vote'] = 0;
				}
				
				$_SESSION['osimo']['perms']['spec'][$forum]['moderate_forum'] = max($perm_can_moderate_forum,$_SESSION['osimo']['perms']['spec'][$forum]['moderate_forum']);
				$_SESSION['osimo']['perms']['spec'][$forum]['view_forum'] = max($perm_can_view_forum,$_SESSION['osimo']['perms']['spec'][$forum]['view_forum']);
				$_SESSION['osimo']['perms']['spec'][$forum]['view_thread'] = max($perm_can_view_thread,$_SESSION['osimo']['perms']['spec'][$forum]['view_thread']);
				$_SESSION['osimo']['perms']['spec'][$forum]['post_thread'] = max($perm_can_post_thread,$_SESSION['osimo']['perms']['spec'][$forum]['post_thread']);
				$_SESSION['osimo']['perms']['spec'][$forum]['post_reply'] = max($perm_can_post_reply,$_SESSION['osimo']['perms']['spec'][$forum]['post_reply']);
				$_SESSION['osimo']['perms']['spec'][$forum]['post_links'] = max($perm_can_post_links,$_SESSION['osimo']['perms']['spec'][$forum]['post_links']);
				$_SESSION['osimo']['perms']['spec'][$forum]['edit'] = max($perm_can_edit,$_SESSION['osimo']['perms']['spec'][$forum]['edit']);
				$_SESSION['osimo']['perms']['spec'][$forum]['create_poll'] = max($perm_can_create_poll,$_SESSION['osimo']['perms']['spec'][$forum]['create_poll']);
				$_SESSION['osimo']['perms']['spec'][$forum]['vote'] = max($perm_can_vote,$_SESSION['osimo']['perms']['spec'][$forum]['vote']);
			}
		}
	}
}
?>