<?php
session_start();
if(!isset($_SESSION['admin'])){ exit; }
if(!isset($osimo))
{
	include_once('../../../os-includes/dbconnect.php');
	include_once('../../../os-includes/paths.php');
	include_once('../../../os-includes/osimo.php');
	$osimo = new Osimo(); 
}
if(!isset($admin))
{
	include_once('../admin.php');
	$admin = new OsimoAdmin();
}
if($_POST['outputGraph']){ outputGraph($_POST['id'],$_POST['startDate'],$_POST['endDate'],$_POST['type']); }
if($_GET['chartXML']){ generateChartXML($_GET['id'],$_GET['startDate'],$_GET['endDate'],$_GET['type']); }
if($_POST['outputTodayStats']){ $admin->outputTodayStats(); }

function outputGraph($id,$startDate,$endDate,$type)
{		
	?>
	<div id="stat-graph">
		

			<EMBED src="includes/charts/charts.swf" 
			       FlashVars='library_path=includes/charts/charts_library&xml_source=<?php echo rawurlencode("includes/ajax/statistics.php?chartXML=true&id=$id&startDate=$startDate&endDate=$endDate&type=$type"); ?>' 
			       quality="high" 
			       bgcolor="#f4f4f4" 
			       WIDTH="480" 
			       HEIGHT="330" 
			       NAME="charts" 
			       allowScriptAccess="sameDomain" 
			       swLiveConnect="true" 
			       loop="false" 
			       scale="noscale" 
			       salign="TL" 
			       align="middle" 
			       wmode="opaque" 
			       TYPE="application/x-shockwave-flash" 
			       PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
			</EMBED>

	</div>
	<?
}

function generateChartXML($id,$startDate,$endDate,$type)
{
	global $osimo,$admin;
	
	$data = $admin->getForumStats($id,$startDate,$endDate,$type);
	
	if(count($data)>5){ $altCol = true; }
	else{ $altCol = false; }
	
	//start the XML output 
	print "<chart>";

	print "<chart_type>";
		print "<string>area</string>";
	print "</chart_type>";
	
	print "<chart_guide horizontal='true'
	             vertical='false'
	             thickness='1' 
	             color='ff4400' 
	             alpha='75' 
	             type='dashed' 
	             
	              
	             radius='8'
	             fill_alpha='0'
	             line_color='ff4400'
	             line_alpha='75'
	             line_thickness='4'
	          
	             size='10'
	             text_color='ffffff'
	             background_color='ff4400'
	             text_h_alpha='90'
	             text_v_alpha='90' 
	             />
	             
	<chart_label position='cursor' />";
	
	print "<series_color>
		<color>51a0d8</color>
	</series_color>";

	print "<chart_data>";
	
	//output the first row that contains the years 
	if($id!=0&&$id!='all'){ $forumName = $osimo->getForumName($id); }
	if($id=='all'){ $forumName = "All Forums"; }
	if($type=='posts'){ $title = "Posts in '$forumName'"; }
	if($type=='views'){ $title = "Views in '$forumName'"; }
	if($type=='newuser'){ $title = "New Registrations"; }
	if(is_array($data))
	{
		print "<row>";
		print "<null/>";
		if($altCol)
		{
			$alt = true;
			foreach($data as $stats)
			{
				if($alt)
				{
					print "<string> </string>";
					$alt = false;
				}
				else
				{
					print "<string>".date("n/j/Y",$stats['date'])."</string>";
					$alt = true;	
				}
			}
		}
		else
		{
			foreach($data as $stats)
			{		
				print "<string>".date("n/j/Y",$stats['date'])."</string>";
			}
		}
		
		print "</row>";
		
		print "<row>";
		print "<string>$title</string>";  
		foreach($data as $stats)
		{
			print "<number>{$stats['count']}</number>";
		}
		print "</row>";
	}
	else
	{
		print "<row>";
		print "<null/>";
		print "<string>".date("n/j/Y",$startDate)."</string>";
		print "<string>".date("n/j/Y",$endDate)."</string>";
		print "</row>";
		print "<row>";
		print "<string>$type</string>";
		print "<number>0</number>";
		print "<number>0</number>";
		print "</row>";
	}
	
	
	//finish the XML output 
	print "</chart_data>";
	print "</chart>";

}

function admin_readFromSysLog($startTime,$endTime,$filter=false)
{
	if(!$filter)
	{
		$query = "SELECT time,type,user,message FROM syslog WHERE time>$startTime AND time<$endTime ORDER BY time ASC";
		$result = mysql_query($query);
	}
	else
	{
		$query = "SELECT time,type,user,message FROM syslog WHERE (time>$startTime AND time<$endTime) AND (";
		if(is_array($filter))
		{
			$i = 0;
			foreach($filter as $item)
			{
				$item = secureContent($item);
				$query .= "type='$item'";
				if($i<(count($filter)-1)){ $query .= " OR "; }
				$i++;
			}
		}
		else
		{
			$query .= "type='$item'";
		}
		$query .= ") ORDER BY time ASC";
		$result = mysql_query($query);
	}
	
	if($result)
	{
		while($data = mysql_fetch_array($result))
		{
			if($data['user']==-1){ $user = 'unknown'; }
			else{ $user = $data['user']; }
			echo "<li><i>".date('m/j/Y g:ia',$data['time'])." - user #$user:</i> {$data['message']}</li>";
		}
	}
}
?>