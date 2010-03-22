<?php
if(!isset($_SESSION['admin'])){ exit; }

function admin_getSiteInfo()
{
	$query = "SELECT name,value FROM config";
	$result = mysql_query($query);
	
	if($result)
	{
		while(list($name,$value)=mysql_fetch_row($result))
		{
			$info[$name] = $value;
		}
	}
	
	return $info;
}
?>