<?php
session_start();
$theurl = explode('os-admin/',$_SERVER['PHP_SELF']);
$url = $theurl[1];
if(isset($_GET)&&count($_GET)>0){
	$url .= "?";
	$i=0;
	foreach($_GET as $key=>$val)
	{
		$url .= "$key=$val";
		if($i<count($_GET)-1)
		{
			$url .= "&";
		}
		$i++;
	}
}
$return = $url;
include('../os-includes/dbconnect.php');
include('../os-includes/paths.php');
include('../os-includes/osimo.php');
include('includes/admin.php');
$osimo = new Osimo();
$admin = new OsimoAdmin();

if(!isset($_SESSION['admin'])){ header('Location: index.php?login=needed&return='.rawurlencode($return)); exit; }
if(!$modpage && $_SESSION['admin']['is_admin']==0){ header('Location: home.php'); }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>Osimo Admin Panel</title>
<script src="<?php echo OSIMOPATH; ?>os-includes/js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo OSIMOPATH; ?>os-includes/js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo OSIMOPATH; ?>os-includes/js/jquery.autocomplete.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo OSIMOPATH; ?>os-admin/js/ajax.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo OSIMOPATH; ?>os-admin/js/backend.js" type="text/javascript" charset="utf-8"></script>
<? if($modpage): ?>
<script src="<?=JS_PATH?>tiny_mce/tiny_mce.js" type="text/javascript" charset="utf-8"></script>
<script>
		tinyMCE.init({
		    theme : "advanced",
		    mode : "exact",
		    elements: 'file-report-content',
		    plugins : "bbcode,safari",
		    theme_advanced_font_sizes : "Extra small text=10px,Small text=12px,Normal text=16px,Big text=24px",
		    theme_advanced_buttons1 : "bold,italic,underline,bullist,blockquote,undo,redo,link,unlink,image,forecolor,removeformat,cleanup,code",
		    theme_advanced_buttons2 : "fontsizeselect,fontselect,styleselect",
		    theme_advanced_buttons3 : "",
		    theme_advanced_toolbar_location : "bottom",
		    theme_advanced_toolbar_align : "center",
		    theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle",
		    content_css : "css/bbcode.css",
		    entity_encoding : "named",
		    add_unload_trigger : false,
		    remove_linebreaks : false,
		    inline_styles : false,
		    convert_fonts_to_spans : false,
		    width: '98%'
		});
</script>
<? endif; ?>
<link rel="stylesheet" href="<?php echo OSIMOPATH; ?>os-admin/styles.css" type="text/css" media="screen" title="no title" charset="utf-8">
<link rel="stylesheet" href="<?php echo OSIMOPATH; ?>os-includes/css/jquery.autocomplete.css" type="text/css" media="screen" title="no title" charset="utf-8">
<link rel="stylesheet" href="<?php echo OSIMOPATH; ?>os-admin/jqueryui/css/ui.all.css" type="text/css" media="screen" title="no title" charset="utf-8">
</head>
<body>
<div id="top">
	<img src="img/admin_logo.jpg" alt="Admin Logo" height="110" width="904">
</div>    
    
<div id="wrap">
	<div id="navigation">
	    <ul>
	    	<li onclick="window.location='home.php'">home</li>
	    	<? if($_SESSION['admin']['is_admin']): ?>
	        	<li onclick="window.location='user.php'">user management</li>
	        	<li onclick="window.location='forum.php'">forum management</li>
	        	<li onclick="window.location='groups.php'">permissions and groups</li>
	        	<li onclick="window.location='theme.php'">visual style</li>
	        	<li onclick="window.location='general.php'">general options</li>
	        	<li onclick="window.location='statistics.php'">statistics</li>
	        <? endif; ?>
	        <li onclick="window.location='mod_panel.php'">moderator panel</li>
		    <li onclick="window.location='logout.php'">logout</li>
	    </ul>
	</div>