<?php
/* Include the header */
include_once(THEMEPATH.'header.php');
?>
<div id="main-content">
	<div id="login-wrap">
		<h3 style="text-align: center">Osimo Registration</h3>
		<form action="os-includes/register.php" method="post" id="osimo_registerform">
			<p class="login-label">Username:</p>
			<p><input type="text" name="osimo_username" id="osimo_username" /></p>
			<p class="login-label">Password:</p>
			<p><input type="password" name="osimo_password1" id="osimo_password" /></p>
			<p class="login-label">Password Again:</p>
			<p><input type="password" name="osimo_password2" id="osimo_password2" /></p>
			<p class="login-label">Email Address:</p>
			<p><input type="text" name="osimo_email1" class="osimo_email" /></p>
			<p class="login-label">Email Again:</p>
			<p><input type="text" name="osimo_email2" class="osimo_email" /></p>
			<p style="text-align: right"><input type="submit" value="Register" id="login-submit" /></p>
		</form>
	</div>
	
</div>
<?php include(THEMEPATH.'footer.php'); ?>