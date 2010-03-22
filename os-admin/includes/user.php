<?php
function admin_getUserList($page,$num,$sort,$dir)
{
	$num = secureContent($num);
	$lower = ($page-1)*$num;
	$sort = secureContent($sort);
	
	$query = "SELECT id,username FROM users ORDER BY $sort $dir LIMIT $lower,$num";
	$result = mysql_query($query);
	if($result)
	{
		$i=0;
		while(list($id,$username)=mysql_fetch_row($result))
		{
			$users[$i]['id'] = $id;
			$users[$i]['username'] = $username;
			$i++;
		}
		
		return $users;
	}
	else
	{
		return false;
	}
}

function admin_getTopUsers($num)
{
	$query = "SELECT id,username FROM users ORDER BY posts DESC LIMIT $num";
	$result = mysql_query($query);
	
	if($result)
	{
		while(list($id,$username)=mysql_fetch_row($result))
		{
			$users[$id] = $username;
		}
		
		return $users;
	}
	else
	{
		return false;
	}
}
?>