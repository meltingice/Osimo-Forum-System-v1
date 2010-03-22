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

if($_POST['content']=='reportlist')
{
	generateReportList($_POST['type'],$_POST['page'],$_POST['num'],
						$_POST['sort_item'],$_POST['sort_order'],
						$_POST['start_date'],$_POST['end_date'],
						$_POST['restrict_user'],$_POST['restrict_mod']);	
}
if($_POST['loadreport'])
{
	loadReport($_POST['loadreport']);
}

//-type/category
//-num per page
//-sort item
//-sort order
//-restrict to user
//-restrict to mod
//-restrict by date
function generateReportList($type=false,$page=1,$num=20,
							$sort_item='date_filed',$sort_order='DESC',
							$start_date=false,$end_date=false,
							$restrict_user=false,$restrict_mod=false)
{
	global $osimo,$admin;
	$start = ($page-1)*$num;
	
	/* Sanitize all SQL data */
	if($type){ $type = secureContent($type); }
	$sort_item = secureContent($sort_item);
	$sort_order = secureContent($sort_order);
	if($start_date){ $start_date = secureContent($start_date); }
	if($end_date){ $end_date = secureContent($end_date); }
	if($restrict_user){ $restrict_user = secureContent($restrict_user); }
	if($restrict_mod){ $restrict_mod = secureContent($restrict_mod); }
	
	/* Now lets build the SQL query */
	$first = true;
	$sql1 = "SELECT * FROM mod_reports";
	$cond = '';
	if($type){ $cond .= " WHERE type='$type'"; $first = false; }
	if($start_date){
		if(!$first){ $cond .= " AND"; }
		else{ $cond .= " WHERE"; $first = false; }
		$cond .= " date_filed>'".strtotime($start_date)."'";
	}
	if($end_date){
		if(!$first){ $cond .= " AND"; }
		else{ $cond .= " WHERE"; $first = false; }
		$cond .= " date_filed<'".strtotime($end_date)."'";
	}
	if($restrict_user){
		if(!$first){ $cond .= " AND"; }
		else{ $cond .= " WHERE"; $first = false; }
		$cond .= " filed_against='$restrict_user'";
	}
	if($restrict_mod){
		if(!$first){ $cond .= " AND"; }
		else{ $cond .= " WHERE"; $first = false; }
		$cond .= " filed_by='$restrict_mod'";
	}
	$cond .= " ORDER BY $sort_item $sort_order LIMIT $start,$num";

	/* Time to run the query */
	$result = mysql_query($sql1.$cond);
	if($result && mysql_num_rows($result)>0)
	{
		$totalReports = mysql_num_rows($result);
		$content = "
		<table cellspacing='0' cellpadding='0' id='reports-table'>
			<tr>
				<td class='reports-table-header'>Type</td>
				<td class='reports-table-header'>Title</td>
				<td class='reports-table-header'>Date Filed</td>
				<td class='reports-table-header'>Filed By</td>
				<td class='reports-table-header'>Filed Against</td>
				<td class='reports-table-header'>Report Excerpt</td>
			</tr>";

		while($data = mysql_fetch_assoc($result))
		{
			$content .= "
			<tr onclick='loadReport({$data['id']})'>
				<td>".reportTypeFormat($data['type'])."</td>
				<td>".reportTitleFormat($data['title'])."</td>
				<td>".date('n/j/y g:i:s a',$osimo->getUserTime($data['date_filed']))."</td>
				<td>{$data['filed_by']}</td>
				<td>".reportFiledAgainstFormat($data['filed_against'])."</td>
				<td>".reportExcerpt($data['report'])."</td>
			</tr>";
		}
		$content .= "</table>";
		
		$query2 = "SELECT COUNT(id) FROM mod_reports";
		$result2 = mysql_query($query2.$cond);
		if($result2 && mysql_num_rows($result2)>0){
			$totalPages = ceil(reset(mysql_fetch_row($result2)) / $num );	
		}
		if($totalPages == 0){ $totalPages = 1; }
		
		$response = array("content" => $content, "totalPages" => number_format($totalPages), "totalReports" => number_format($totalReports));
		echo json_encode($response);
	}
	else
	{
		$content = "<h4 id='empty-report'>Sorry, there are no matches for your search criteria!</h4>";
		$reponse = array("content"=>$content,"totalPages" => "1", "totalReports" => "0");
		
		echo json_encode($reponse);
	}
}

function loadReport($id)
{
	global $osimo,$admin;
	
	$id = secureContent($id);
	
	$sql = "SELECT * FROM mod_reports WHERE id='$id'";
	$result = mysql_query($sql);
	
	if($result && mysql_num_rows($result)>0)
	{
		$data = mysql_fetch_assoc($result);
		
		$data['id'] = $id;
		$data['type'] = reportTypeFormat($data['type']);
		$data['title'] = reportTitleFormat($data['title']);
		$data['date_filed'] = date('F j, Y \a\t g:i:s a',$osimo->getUserTime($data['date_filed']));
		$data['filed_against_username'] = reportFiledAgainstFormat($data['filed_against_username']);
		if($data['filed_against']==0){
			$data['filed_info'] = " by {$data['filed_by_username']}";
		}
		else{
			$data['filed_info'] = " by <strong>{$data['filed_by_username']}</strong> against <strong>{$data['filed_against_username']}</strong>";
		}
		
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array("error"=>"fail"));
	}
}

function reportTypeFormat($str)
{
	$data['warning'] = "<img src='img/icons/error.png' alt='warning' />&nbsp;Warning";
	$data['ban'] = "<img src='img/icons/delete.png' alt='banned' />&nbsp;Ban";
	$data['p_del'] = "<img src='img/icons/note_delete.png' alt='deleted' />&nbsp;Post: Delete";
	$data['t_move'] = "<img src='img/icons/information.png' alt='info' />&nbsp;Thread: Move";
	$data['t_del'] = "<img src='img/icons/information.png' alt='info' />&nbsp;Thread: Delete";
	$data['t_sticky'] = "<img src='img/icons/information.png' alt='info' />&nbsp;Thread: Sticky";
	$data['t_lock'] = "<img src='img/icons/information.png' alt='info' />&nbsp;Thread: Lock";
	$data['general'] = "<img src='img/icons/note.png' alt='note' />&nbsp;General/Other";
	
	return $data[$str];
}

function reportTitleFormat($str)
{
	if(!$str){
		return "Untitled";
	}
	
	return ucfirst($str);
}

function reportFiledAgainstFormat($username)
{
	if(!$username || strlen($username) == 0)
	{
		return "N/A";
	}
	else
	{
		return $username;
	}
}

function reportExcerpt($report)
{
	if(strlen($report)<=50){
		return $report;
	}
	else{
		return substr($report,0,47)."...";
	}
}
?>