<?php
$sig = $osimo->getUserSignature($post['poster']);
$userdata = $osimo->getUserInfo($post['poster'],array('posts','time_joined','ip_address'));
$userOnline = $osimo->isUserOnline($post['poster']);
$userColor = $osimo->getUsernameColor($post['poster']);
?>

<div class="forum-post">
	<div class="post-user-info">
		<? if($userOnline): ?>
			<div class="user-online"></div>
		<? endif; ?>
		<div class="user-avatar"><?=$osimo->getUserAvatar($post['poster']); ?></div>
		<div class="user-info">
			<h4><a href="profile.php?id=<?=$post['poster']?>" <? if($userColor){ echo "style=\"color:$userColor;\""; } ?>><?=$post['poster_username']?></a></h4>
			<br />
			<h4>User Info</h4>
			<p>Posts: <?=$userdata['posts']?></p>
			<p>Joined: <?=date('D M j, Y g:ia',$osimo->getUserTime($userdata['time_joined']));?></p>
			<? if($osimo->userIsAdmin()||$osimo->userIsModerator()): ?>
			<p>IP: <?=$userdata['ip_address']?></p>
			<? endif; ?>
			<br />
			<h4>Message Info</h4>
			<p><img src="<?=THEMEPATH?>img/icons/time.png" />&nbsp;Posted: <?=date('D M j, Y g:ia',$osimo->getUserTime($post['post_time']));?></p>
		</div>
	</div>
	<div class="post-action-bar">
		<? if($postPreview): ?>
			<div class="post-preview-msg"><p><img src="<?=THEMEPATH?>img/icons/error.png" />&nbsp;This is a post preview</p></div>
		<? endif; ?>
	</div>
	<div id="post-<?=$post['id']?>" class="post-content" <? if(!$sig){ echo 'style="padding-bottom: 10px;"'; } ?>>
		<?=$post['body']?>
	</div>
	<div class="post-sig">
	<? if($sig): ?>
		<p>------------------</p>
		<p><?=$sig?></p>
	<? endif; ?>
	</div>
</div>