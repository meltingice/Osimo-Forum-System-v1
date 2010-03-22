<? $user = $osimo->getLoggedInUser(); ?>
<div id="navbar">
	<div id="navbar-upper">
		<p>
			<img src="<?=THEMEPATH?>img/icons/house.png" alt="Home" />&nbsp;<a href="index.php">Board Home</a> &bull; 
			<img src="<?=THEMEPATH?>img/icons/magnifier.png" alt="Search" />&nbsp;<a href="search.php">Search</a> &bull;
			<img src="<?=THEMEPATH?>img/icons/group.png" alt="Memberlist" />&nbsp;<a href="#" onclick="showMemberList()" />Memberlist</a> &bull;
			<? if($user): ?>
				<img src="<?=THEMEPATH?>img/icons/user_delete.png" alt="Logout" />&nbsp;<a href="logout.php">Logout</a>
			<? else: ?>
				<img src="<?=THEMEPATH?>img/icons/key.png" alt="Login" />&nbsp;<a href="login.php">Login</a> &bull;
				<img src="<?=THEMEPATH?>img/icons/user_add.png" alt="Register" />&nbsp;<a href="register.php">Register</a>
			<? endif; ?>
		</p>
		<? if($user): ?>
			<p id="navbar-welcome">
				Welcome, <strong><?=$user['name']?></strong>!
				<? if($osimo->userIsAdmin()): ?>
					&bull; <a href="os-admin/">Osimo Admin Panel</a>
				<? endif; ?>
				<? if($osimo->userIsModerator()): ?>
					&bull; Moderator
				<? endif; ?>
			</p>
		<? endif; ?>
	</div>
	<div id="navbar-lower">
		<p <? if(!$user){ echo "style=\"color:#888888\""; } ?>>
		<? if($user): ?>
			<img src="<?=THEMEPATH?>img/icons/email_open.png" alt="Inbox" />&nbsp;<a href="messages.php">Inbox (<?=$osimo->getNumUnreadMessages();?>)</a> &bull;
			<img src="<?=THEMEPATH?>img/icons/user_green.png" alt="Profile" />&nbsp;<a href="profile.php">Profile</a> &bull;
			<img src="<?=THEMEPATH?>img/icons/cog.png" alt="User CP" />&nbsp;<a href="#" onclick="userCP()">User Control Panel</a>
		<? else: ?>
			<img src="<?=THEMEPATH?>img/icons/email_open_disabled.png" alt="Inbox" />&nbsp;Inbox &bull;
			<img src="<?=THEMEPATH?>img/icons/user_green_disabled.png" alt="Profile" />&nbsp;Profile &bull;
			<img src="<?=THEMEPATH?>img/icons/cog_disabled.png" alt="User CP" />&nbsp;User Control Panel</a>
		<? endif; ?>
		</p>
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
