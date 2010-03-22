<?php
session_start();
if(!isset($_SESSION['admin'])){ exit; }
include_once('../../../os-includes/dbconnect.php');
include_once('../../../os-includes/paths.php');
include_once(ABS_INCLUDES.'security.php');

if(isset($_POST['query'])&&$_POST['query']!=''){ userSearch($_POST['query']); }

function userSearch($q)
{
	$q = secureContent($q);
	
	$query = "SELECT id,username FROM users WHERE username LIKE '%$q%' ORDER BY username ASC LIMIT 0,20";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		echo "<div id=\"user_ac_wrap\">\n";
		echo "<ul id=\"user_ac_results\">\n";
		while(list($id,$username)=mysql_fetch_row($result))
		{
			echo "<li id=\"user_$id\" onclick=\"loadUserInfo($id)\">$username</li>\n";
		}
		echo "</ul></div>\n";
	}
}
mysql_close();
?>