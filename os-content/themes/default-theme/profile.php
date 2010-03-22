<?php
/* Include the header */
include_once(THEMEPATH.'header.php');
?>

<div id="main-content" class="profile-content">
	<div class="profile-header-container">
		<div class="profile-header">
			<div class="profile-header-left">
				<?php if(isset($_GET['id'])){ echo $osimo->getUserAvatar($_GET['id']); }
					else{ echo $osimo->getUserAvatar(); }
				?>
			</div>
			<div class="profile-header-right">
				<h4 ><?php echo $user_info['username']; ?></h4>
				<h4 style="font-size: 11px;">Member Since: <?php echo adodb_date($user_info['time_format'],$osimo->getUserTime($user_info['time_joined'])); ?></h4>
				<?php
				if($user&&(!isset($_GET['id'])||$_GET['id']==$user['ID'])):
				?>
					<h4 style="margin-top: 0px;font-size: 11px;cursor:pointer;"><a href="javascript:editProfile(<?php echo $user['ID']; ?>)">[Edit Profile]</a></h4>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="right-content">
		<h4><small>Contact Information</small></h4>
		<ul>
			<li><strong>AIM:</strong> <?php echo $user_info['field_aim']; ?></li>
			<li><strong>Jabber:</strong> <?php echo $user_info['field_jabber']; ?></li>
			<li><strong>MSN:</strong> <?php echo $user_info['field_msn']; ?></li>
			<li><strong>Yahoo IM:</strong> <?php echo $user_info['field_yim']; ?></li>
			<li><strong>ICQ:</strong> <?php echo $user_info['field_icq']; ?></li>
			<li><a href="messages.php?sendto=<?php echo $user_info['username']; ?>">Send Personal Message</a></li>
		</ul>
	</div>
	<div class="left-content">
		<h4><small>User Information</small></h4>
		<ul>
			<li><strong>Sex:</strong> <?php echo $user_info['field_sex']; ?></li>
			<li><strong>Age:</strong> <?php echo $user_info['field_age']; ?></li>
			<li><strong>Birthday:</strong> <?php echo adodb_date('n/j/Y',$user_info['birthday']); ?></li>
			<li><strong>Website:</strong> <?php echo $user_info['field_website']; ?></li>
			<li><strong>About:</strong> <?php echo $user_info['field_about']; ?></li>
			<li><strong>Interests:</strong> <?php echo $user_info['field_interests']; ?></li>
		</ul>
	</div>
	<div class="left-content">
		<h4><small>Statistics</small></h4>
		<ul>
			<li><strong>Posts:</strong> <?php echo $user_info['posts']; ?></li>
			<li><strong>Last Visit:</strong> <?php echo adodb_date($user_info['time_format'],$osimo->getUserTime($user_info['time_last_visit'])); ?></li>
			<li><strong>Last Post:</strong> <?php echo adodb_date($user_info['time_format'],$osimo->getUserTime($user_info['time_last_post'])); ?></li>
			<li><strong>Last Page:</strong> <?php echo $user_info['last_page']; ?></li>
			<li><strong>User's Local Time:</strong> <?php 
				if(isset($_GET['id'])){ echo adodb_date($user_info['time_format'],$osimo->getUserLocalTime($_GET['id'])); }
				else{ echo adodb_date($user_info['time_format'],$osimo->getUserLocalTime()); } ?>
			</li>
		</ul>
	</div>
	<div class="profile-other-info">
		<h4><small>Biography</small></h4>
		<div id="profile_bio">
			<p><?php echo $user_info['field_biography']; ?></p>
		</div>
	</div>
	<div class="profile-other-info">
		<h4><small>Signature</small></h4>
		<div id="profile_sig">
			<p>
			<?php
				if(isset($_GET['id'])){ echo $osimo->getUserSignature($_GET['id'],true,false); }
				else{ echo $osimo->getUserSignature(); }
			?>
			</p>
		</div>
	</div>
</div>

<?php include(THEMEPATH.'footer.php'); ?>