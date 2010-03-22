<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/memberlist.php - functions relating to the member list
*	Functions should be accessed through the Osimo class
*/

function _getMemberList($page,$rows,$sort,$sortDir)
{
	# Preparation
	$count = 0;
	$rows = secureContent($rows);
	$lower = ($page-1)*$rows;
	$sort = secureContent($sort);
	$sortDir = secureContent($sortDir);
	
	# Prepare The Query
	$query = "SELECT id, username, birthday, username_style, username_color, rank_level, rank_status, posts, is_admin, is_global_mod FROM users ORDER BY is_admin DESC,is_global_mod DESC,$sort $sortDir LIMIT $lower,$rows";
	$result = mysql_query($query);
	
	while($fetch_users = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if($fetch_users['is_admin'] == 1) { $admin_set = "Admin"; }
		elseif($fetch_users['is_global_mod'] == 1) { $admin_set = "Global Mod"; }
		else { $admin_set = ""; }
		
		$memberlist[$count]['id'] = $fetch_users['id'];
		$memberlist[$count]['username'] = "<a href='profile.php?id=" . $fetch_users['id'] . "' style='color:" . $fetch_users['username_color'] . ";" . $fetch_users['username_style'] . "'>" . $fetch_users['username'] . "</a>";
		$memberlist[$count]['status'] = $admin_set;
		$memberlist[$count]['birthday'] = adodb_date("n/j/Y", $fetch_users['birthday']);
		$memberlist[$count]['rank'] = $fetch_users['rank_status'];
		$memberlist[$count]['posts'] = $fetch_users['posts'];
	
		$count++;
	}
	
	return $memberlist;
}