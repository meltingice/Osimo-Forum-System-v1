<div id="navbar" class="clearfix">
	<p style="width: 650px; float: left;"><a href="index.php">Home</a> |  
    <?php
		$user = $osimo->getLoggedInUser();
		if($user)
		{
			?>
            	<a href="messages.php">Inbox (<?php echo $osimo->getNumUnreadMessages(); ?>)</a> | <a href="profile.php?id=<?php echo $user['ID']; ?>">Profile</a> | <a href="#" onclick="userCP()">User CP</a> | <a href="search.php">Search</a> | <a href="#" onclick="showMemberList()">Members</a> | <a href="logout.php">Logout</a> | Welcome, <?php echo $user['name']; ?>
            <?php
		}
		else
		{
			?>
            	<a href="login.php">Login</a> | <a href="#" onclick="showMemberList()">Members</a> | <a href="register.php">Register</a> | Welcome, Guest
            <?php
		}
	?>
    </p>
    <div id="loading" style="display:none">
    	<img src="<?php echo THEMEPATH; ?>images/ajax-loader.gif" alt="loading" style="float: left;" />
    	<p style="float:right"> Loading...</p>
    </div>
</div>

<div id="alertbox" style="display:none">
	<p id="alert-contents"><?php
		$alert = $osimo->getAlertMsg();
		if($alert)
		{
			echo $alert;
			?>
			<script>
				showAlert();
			</script>
			<?
		}
		?></p>
</div>
