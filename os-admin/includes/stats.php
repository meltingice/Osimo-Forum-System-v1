<?php
if(!isset($_SESSION['admin'])){ exit; }

function admin_getTodayStats($type)
{
	global $osimo;
	$type = secureContent($type);
	$today = $osimo->getTodayTimestamp();
	$query = "SELECT count FROM stats WHERE date='$today' AND type='$type'";
	$result = mysql_query($query);
	if($result&&mysql_num_rows($result)>0)
	{
		$num=0;
		while(list($count)=mysql_fetch_row($result))
		{
			$num=$num+$count;
		}
		
		return number_format($num);
	}
	else
	{
		return 0;
	}
}

function admin_getForumStats($id,$startDate,$endDate,$type)
{
	$id = secureContent($id);
	$startDate = secureContent($startDate);
	$endDate = secureContent($endDate);
	$type = secureContent($type);
	
	if($id=='all')
	{
		$query = "SELECT date,count FROM stats WHERE (date>=$startDate AND date<=$endDate) AND type='$type' ORDER BY date ASC";
		$result = mysql_query($query);
		if($result&&mysql_num_rows($result)>0)
		{
			while(list($date,$count)=mysql_fetch_row($result))
			{
				$stats[$date]['date'] = $date;
				$stats[$date]['count'] = $stats[$date]['count']+ $count;
			}
			
			return $stats;
		}
		else
		{
			return false;
		}
	}
	else
	{
		$query = "SELECT date,count FROM stats WHERE (date>=$startDate AND date<=$endDate) AND type='$type' and forumID='$id' ORDER BY date ASC";
		$result = mysql_query($query);
		if($result&&mysql_num_rows($result)>0)
		{
			$i=0;
			while(list($date,$count)=mysql_fetch_row($result))
			{
				$stats[$i]['date'] = $date;
				$stats[$i]['count'] = $count;
				$i++;
			}
			
			return $stats;
		}
		else
		{
			return false;
		}
	}
	
}

function admin_outputTodayStats()
{
	global $admin;
	?>
	<table id="today-stats">
		<tr>
		    <td class="stat-number"><?php echo $admin->getTodayStats('posts'); ?></td>
		    <td class="stat-title">posts</td>
		    <td class="stat-number"><?php echo $admin->getTodayStats('threads'); ?></td>
		    <td class="stat-title">new threads</td>
		</tr>
		<tr>
		    <td class="stat-number"><?php echo $admin->getTodayStats('newuser'); ?></td>
		    <td class="stat-title">new users</td>
		    <td class="stat-number"><?php echo $admin->getTodayStats('views'); ?></td>
		    <td class="stat-title">page views</td>
		</tr>
	</table>
	<?
}
?>