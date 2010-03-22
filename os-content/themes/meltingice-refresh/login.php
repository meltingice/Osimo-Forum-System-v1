<?php
/* Include the header */
include_once(THEMEPATH.'header.php');
?>

<div id="main-content">
	<div id="login-wrap">
		<h3 style="text-align: center">Osimo Login</h3>
		<form action="os-includes/login.php" method="post" id="login-form">
			<p class="login-label">Username</p>
			<p><input type="text" name="osimo_username" id="osimo_username" /></p>
			<p class="login-label">Password</p>
			<p><input type="password" name="osimo_password" id="osimo_password" /></p>
			<p style="text-align: right">
			<input type="checkbox" name="osimo_rememberme" /> <label id="osimo_rememberme" for="osimo_rememberme">Remember Me</label>
			<input type="submit" value="Login" id="login-submit" /></p>
			<p class="forgot-password"><a href="#" onclick="forgotPassword()">Forgot Password</a></p>
		</form>
	</div>
</div>

<?php include_once(THEMEPATH.'footer.php'); ?>