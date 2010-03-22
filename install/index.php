<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	install.php - installs Osimo onto a server
*	This might be a bit messy because everything
*	is contained in one page.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>Osimo Forum System - Install Script</title>
	<script src="../os-includes/js/jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="backend.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="styles.css" type="text/css" media="screen" title="no title" charset="utf-8">
</head>
<body>
<body>
<div id="wrap">
	<div id="osimo-title">
		<h4>osimo</h4>
		<p>install script</p>
	</div>
	<div class="content-wrap">
		<div id="page-1" class="content-body">
			<h4>Welcome to Osimo!</h4>
			<p>All we need from you is a few clicks and a little bit of info so we can get started installing Osimo!  There are only 3 steps to this installation procedure, so you'll have Osimo up and running in no time.</p>
			<input type="button" value="Next Step" class="next-step" onclick="loadStep(2)" />
		</div>
		<div id="page-2" class="content-body" style="display:none">
			<h4>Step 1: Database Installation</h4>
			<p>Osimo needs to create the necessary database tables in order to function properly.  Click the button below to install the database tables.</p>
			<p style="text-align: center"><input type="button" value="Install Database" class="generic-button" onclick="runInstall('database')" /> Status: <span id="database-status">Idle</span></p>
			<p>When the database is finished installing, you may continue installation and move on to the next step.</p>
			<input type="button" value="Next Step" class="next-step" onclick="loadStep(3)" disabled="true" />
		</div>
		<div id="page-3" class="content-body" style="display:none">
			<h4>Step 2: Database Setup</h4>
			<p>We need to insert some default values into the database so that Osimo can function properly.  We also need your input here to customize Osimo to your preferences.</p>
			<p class="input-label">Site Title</p>
			<p><input class="input-field" type="text" id="option-site-title" /></p>
			<p class="input-label">Site Description</p>
			<p><input class="input-field" type="text" id="option-site-desc" /></p>
			<p class="input-label">Admin Email</p>
			<p><input class="input-field" type="text" id="option-admin-email" /></p>
			<p>Once you have filled out all the fields above, click the button below to configure the database with your options.</p>
			<p style="text-align: center"><input type="button" value="Configure Database" class="generic-button" onclick="runInstall('config')" /> Status: <span id="config-status">Idle</span></p>
			<p>When the database is finished being configured, click the button below to go to the next step.</p>
			<input type="button" value="Next Step" class="next-step" onclick="loadStep(4)" disabled="true" />
		</div>
		<div id="page-4" class="content-body" style="display:none">
			<h4>Step 3: Admin Setup</h4>
			<p>Osimo needs to set up at least one admin user so that he/she can access the admin panel, add more admins if necessary, and further configure this Osimo installation.</p>
			<p class="input-label">Admin Username</p>
			<p><input class="input-field" type="text" id="option-admin-name" /></p>
			<p class="input-label">Admin Password</p>
			<p><input class="input-field" type="password" id="option-admin-password1" /></p>
			<p class="input-label">Retype Admin Password</p>
			<p><input class="input-field" type="password" id="option-admin-password2" /></p>
			<p>Once you have filled out all the fields above, click the button below to add your admin account to the database.</p>
			<p style="text-align: center"><input type="button" value="Create Admin" class="generic-button" onclick="runInstall('admin')" /> Status: <span id="admin-status">Idle</span></p>
			<p>When the database is finished adding you as an admin, click the button below to continue.</p>
			<input type="button" value="Next Step" class="next-step" onclick="loadStep(5)" disabled="true" />
		</div>
		<div id="page-5" class="content-body" style="display:none">
			<h4>Installation Finished!</h4>
			<p>That's it! Now that wasn't too painful was it?  Now that Osimo is installed, you need to visit the admin panel to setup the forum structure so users can start posting.  Click the button below to go to the Admin Panel.  Use the Admin username and password you entered in the previous step to log in.</p>
			<p class="warning">It is highly recommended that you delete this installation directory now that you are finished for security reasons.  You should probably do it now so you don't forget to later!</p>
			<input type="button" value="Next Step" class="next-step" onclick="window.location='../os-admin/index.php'" />
		</div>
	</div>
</div>
</body>
</html>