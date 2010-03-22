<?php
session_start();
//if(isset($_SESSION['admin'])){ header('Location: home.php'); exit; }
include('../os-includes/paths.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>Osimo Admin Panel</title>
	<script src="<?php echo OSIMOPATH; ?>os-includes/js/jquery.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="<?php echo OSIMOPATH; ?>os-admin/login.css" type="text/css" media="screen" title="no title" charset="utf-8">
</head>
<body>
<div id="wrap">
	<div id="login-logo"></div>
	<div id="login-wrap">
		<form action="login.php" method="post" id="login-form">
			<p class="input-label">Username</p>
			<p class="input-field"><input type="text" name="osimo_username" id="osimo_username" /></p>
			<p class="input-label">Password</p>
			<p class="input-field"><input type="password" name="osimo_password" id="osimo_password" /></p>
			<?php if(isset($_GET['return'])): ?>
			<input type="hidden" name="return" value="<?php echo $_GET['return']; ?>" />
			<?php endif; ?>
			<p><input id="login-button" type="submit" value="Login" /></p>
		</form>
	</div>
</div>

</body>
</html>