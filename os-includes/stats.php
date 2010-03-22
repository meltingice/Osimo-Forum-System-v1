<?php
function _addPageView($type,$id)
{
	global $osimo;
	
	$id = secureContent($id);
	if($type!='forum'&&$type!='thread'&&$type!='private_message_thread'&&$type!='other'){ exit; }
	
	if($type=='forum'){
		if(!$osimo->userCanViewForum($id)){ exit; }
		
		$lastpage = "<a href=\"forum.php?id=$id\">".$osimo->getForumName($id)."</a>";
	}
	if($type=='thread'){
		if(!$osimo->userCanViewThreads()){ exit; }
		
		$forum = $osimo->getThreadForum($id);
		if(!$osimo->userCanViewThreads($forum['id'])){ exit; }
		
		$thread = $osimo->getThreadName($id);
		$lastpage = "<a href=\"forum.php?id={$forum['id']}\">{$forum['title']}</a> / <a href=\"thread.php?id=$id\">$thread</a>";
	}
	if($type=='private_message_thread')
	{
		if(!$osimo->userCanReceivePM()){ exit; }
		$lastpage = false;
	}
	if($type=='other')
	{
		$lastpage = false;
	}
	$theTime = time();
	
	/* running tally stats */
	if($type!='other')
	{
		$query1 = "UPDATE $type SET views=views+1 WHERE id='$id' LIMIT 1";
		$result1 = mysql_query($query1);
	
		/* user stats */
		$user = $osimo->getLoggedInUser();
		if($lastpage!=false&&$user!=false)
		{
			$query2 = "UPDATE users SET last_page='$lastpage',time_last_visit='$theTime',last_page_type='$type',last_page_id='$id' WHERE id='{$user['ID']}' LIMIT 1";
			$result2 = mysql_query($query2);
		}
		if($lastpage==false&&$user!=false)
		{
			$query2 = "UPDATE users SET time_last_visit='$theTime',last_page_type='other',last_page_id='0' WHERE id='{$user['ID']}' LIMIT 1";
			$result2 = mysql_query($query2);
		}
	}
	
	/* per forum stats */
	if($type=='forum')
	{
		$statID = $id;
	}
	if($type=='thread')
	{
		$statID = $forum['id'];
	}
	if($type=='other')
	{
		$statID = $id;
	}
	
	if($type=='forum'||$type=='thread'||$type=='other')
	{
		$today = $osimo->getTodayTimestamp();
		$query3 = "SELECT COUNT(*) FROM stats WHERE date='$today' AND forumID='$statID' AND type='views' LIMIT 1";
		$result3 = mysql_query($query3);
		if($result3)
		{
			if(reset(mysql_fetch_row($result3))==0)
			{
				$insert = "INSERT INTO stats (forumID,date,type,count) VALUES ($statID,$today,'views','1')";
				$result4 = mysql_query($insert);
			}
			else
			{
				$update = "UPDATE stats SET count=count+1 WHERE forumID='$statID' AND date='$today' AND type='views' LIMIT 1";
				$result4 = mysql_query($update);
			}
			
			if($result4){ return true; }
			else{ return false; }
		}
	}
}
?>