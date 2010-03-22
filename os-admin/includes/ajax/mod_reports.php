<?
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

if($_POST['choosereporttype']){
	fetchReportPresets($_POST['choosereporttype']);
}
if($_POST['load_template']){
	loadReportTemplate($_POST['load_template']);
}
if($_POST['save_template']){
	saveReportTemplate($_POST['template_type'],$_POST['template_title'],$_POST['template_report']);
}
if($_POST['send_report']){
	saveReport($_POST['report_type'],$_POST['report_title'],$_POST['report_content'],$_POST['report_file_against'],$_POST['report_concerning_id']);
}
if($_POST['deleteReport']){
	deleteReport($_POST['deleteReport']);
}
function fetchReportPresets($type)
{
	$type = secureContent($type);
	
	$query = "SELECT id,title FROM mod_report_templates WHERE type='$type'";
	$result = mysql_query($query);
	
	if($result && mysql_num_rows($result)>0)
	{
		$html .= "<option value='0'>Choose Template:</option>";
		while($data = mysql_fetch_assoc($result))
		{
			$html .= "<option value='{$data['id']}'>{$data['title']}</option>\n";
		}
		
		$response = array("content" => $html, "count" => mysql_num_rows($result));
	}
	else
	{
		$html = "<option value='0'>N/A</option>\n";
		
		$response = array("content" => $html, "count" => "0");
	}
	
	echo json_encode($response);
}

function loadReportTemplate($id)
{
	$id = secureContent($id);
	
	$query = "SELECT title,report FROM mod_report_templates WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	
	if($result && mysql_num_rows($result)>0)
	{
		$data = mysql_fetch_assoc($result);
		
		echo json_encode($data);
	}
}

function saveReportTemplate($type,$title,$report)
{
	global $osimo;
	$type = secureContent($type);
	$title = secureContent(stripslashes(rawurldecode($title)));
	$report = htmlspecialchars(secureContent(rawurldecode(html_entity_decode($report,ENT_NOQUOTES,'UTF-8'))));
	
	/* Check for duplicate first */
	$query = "SELECT COUNT(id) FROM mod_report_templates WHERE type='$type' AND title='$title' LIMIT 1";
	$result = mysql_query($query);
	
	if($result && reset(mysql_fetch_row($result)) != 0) //duplicate template
	{
		echo json_encode(array("response"=>"This template name is already taken, please choose a different one!"));
	}
	else
	{
		$created_by = $osimo->getLoggedInUser();
		$query2 = "INSERT INTO mod_report_templates (type,title,report,created_by,date_created) VALUES ('$type','$title','$report','{$created_by['ID']}','".time()."')";
		$result2 = mysql_query($query2);
		
		if($result2){
			echo json_encode(array("response"=>"1"));
		}
		else{
			echo json_encode(array("response"=>"Template add failed"));
		}
	}
}

function saveReport($type,$title,$report,$file_against,$concerning_id)
{
	global $osimo;
	$type = secureContent($type);
	$title = secureContent(stripslashes(rawurldecode($title)));
	$report = htmlspecialchars(secureContent(rawurldecode(html_entity_decode($report,ENT_NOQUOTES,'UTF-8'))));
	$file_against = htmlspecialchars(secureContent(rawurldecode(html_entity_decode($file_against,ENT_NOQUOTES,'UTF-8'))));
	$concerning_id = secureContent($concerning_id);
	
	$user = $osimo->getLoggedInUser();
	
	/* Insert into mod_reports */
	$query = "INSERT INTO mod_reports (type,title,report,filed_by_id,filed_by,filed_against_id,filed_against,concerning_id,date_filed) VALUES ('$type','$title','$report','{$user['ID']}','{$user['name']}'";
	if($file_against==''){ $query .= ",'0',''"; }
	else{ $query .= ",(SELECT id FROM users WHERE username='$file_against' LIMIT 1),'$file_against'"; }
	if($concerning_id==''){ $query .= ",'0'"; }
	else{ $query .= ",'{$concerning_id}'"; }
	$query .= ", '".time()."')";

	$result = mysql_query($query);
	
	/* Add warning if type is warning */
	if($type=='warning'){
		$userID = $osimo->userNametoID($file_against);
		if($concerning_id==''){ $concerning_id = 0; }
		$osimo->createWarning($userID,$concerning_id);
	}
	
	if($result){
		echo json_encode(array("response"=>"1"));
	}
	else
	{
		echo json_encode(array("response"=>"0"));
	}
}

function deleteReport($id)
{
	$id = secureContent($id);
	
	$query = "DELETE FROM mod_reports WHERE id='$id' LIMIT 1";
	$result = mysql_query($query);
	
	if($result){ echo "1"; }
	else{ echo "0"; }
}
?>