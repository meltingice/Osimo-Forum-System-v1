<?
function allowLogin($user_id=false,$ip_address=false,$and=false)
{
	if($user_id==false&&$ip_address==false){ return true; }
	$query = "SELECT COUNT(*) FROM bans WHERE";
	if($user_id&&!$ip_address){
		if(!is_numeric($user_id)){ return true; }
		$query .= " user_id='$user_id'";
	}
	elseif(!$user_id&&$ip_address){
		$ip_address = mysql_real_escape_string($ip_address);
		$query .= " ip_address='$ip_address'";
	}
	else{
		if(!is_numeric($user_id)){ return true; }
		$ip_address = mysql_real_escape_string($ip_address);
		if($and){ $switch = 'AND'; }
		else{ $switch = 'OR'; }
		$query .= " (user_id='$user_id' $switch ip_address='$ip_address')";
	}
	$query .= " AND (ban_expire='0' || ban_expire>'".time()."') LIMIT 1";
	$result  = mysql_query($query);
	if($result){;
		if(reset(mysql_fetch_row($result))){
			return false;
		}
	}
	
	return true;
}
?>