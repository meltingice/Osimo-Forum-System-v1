<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php $osimo->getHeader(); ?>
	<script src="<?php echo THEMEPATH; ?>js/effects.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="<?php echo THEMEPATH; ?>styles.css" type="text/css" media="screen" title="no title" charset="utf-8" />
	<link rel="stylesheet" href="<?php echo THEMEPATH; ?>jquery-ui-themeroller.css" type="text/css" media="screen" title="no title" charset="utf-8" />
</head>
<body>

<!-- Temporary Navigation Panel
<div>
<table border='0' cellpadding='4' cellspacing='0'>
	<tr>
		<td><? $osimo->outputUnorderedList($osimo->getNavigationList()); ?></td>
	</tr>
</table>
</div>
-->

<div id="header_title">
	<h4><?php echo $osimo->getSiteTitle(); ?></h4>
	<p><?php echo $osimo->getSiteDescription(); ?></p>
</div>

<?php include_once(THEMEPATH.'navbar.php'); ?>