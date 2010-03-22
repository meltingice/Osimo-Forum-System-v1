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
if(isset($_POST['updateOptions']))
{
	$options['site_title'] = $_POST['siteTitle'];
	$options['site_description'] = $_POST['siteDesc'];
	$options['admin_email'] = $_POST['adminEmail'];
	$options['server_time_zone'] = $_POST['serverTimeZone'];
	$options['email_new_user'] = $_POST['newUserEmail'];
	$options['registration'] = $_POST['registration'];
	$options['thread_num_per_page'] = $_POST['numThreads'];
	$options['post_num_per_page'] = $_POST['numPosts'];
	updateOptions($options);
}

function updateOptions($options)
{
	if(!is_numeric($options['thread_num_per_page'])||!is_numeric($options['post_num_per_page'])){ echo "0"; exit; }
	if($options['thread_num_per_page']<=0||$options['post_num_per_page']<=0){ echo "0"; exit; }
	
	foreach($options as $name=>$value)
	{
		$value = stripslashes($value);
		$query = "UPDATE config SET value='$value' WHERE name='$name' LIMIT 1";
		$result = mysql_query($query);
	}
	
	echo "1";
}
?>