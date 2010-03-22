<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php $osimo->getHeader(); ?>
	<script src="<?php echo THEMEPATH; ?>js/effects.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="<?php echo THEMEPATH; ?>styles.css" type="text/css" media="screen" title="no title" charset="utf-8" />
	<link rel="stylesheet" href="<?php echo THEMEPATH; ?>jqueryui/css/ui.all.css" type="text/css" media="screen" title="no title" charset="utf-8" />
	<script>
		var editor;
		$(document).ready(function(){
			editor = new OsimoEditor(
				["#osimo_postbox",
				"#osimo_newmessagepost",
				"#osimo_messagepost",
				"#osimo_messagecontent",
				"#osimo_newthreadpost"],
				{
					"width":"95%",
					"height":"200px",
					"styles":
					{
						"margin":"0 auto"
					}
				});
		});
	</script>
</head>
<body>

<div id="header">
	<div id="header_title">
		<h4><?php echo $osimo->getSiteTitle(); ?></h4>
		<p><?php echo $osimo->getSiteDescription(); ?></p>
	</div>
</div>

<?php include_once(THEMEPATH.'navbar.php'); ?>