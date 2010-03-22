<?
	define("THEMEPATH","os-content/themes/meltingice-refresh/");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script src="os-includes/js/jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="os-includes/js/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
	<script src="os-includes/js/osimo_editor/osimo_editor.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="<?php echo THEMEPATH; ?>jqueryui/css/ui.all.css" type="text/css" media="screen" title="no title" charset="utf-8" />
	<script>
		var editor;
		$(document).ready(function(){
			editor = new OsimoEditor(["#osimo_postbox"],{"width":"550px","height":"200px"});
		});
	</script>
</head>

<textarea id="osimo_postbox"></textarea>