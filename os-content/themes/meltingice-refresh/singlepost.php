<?php 
/*
*	This might seem a bit strange at first, but this allows us to use ajax easily
*	Remember, this file is loaded "in the loop" for each post
*/

$sig = $osimo->getUserSignature($post['poster_id']);
$userdata = $osimo->getUserInfo($post['poster_id'],array('posts','time_joined','ip_address'));
$userOnline = $osimo->isUserOnline($post['poster_id']);
$warnings = $osimo->getUserWarnings($post['poster_id']);
$userColor = $osimo->getUsernameColor($post['poster_id']);
?>
<div class="forum-post">
	<div class="post-user-info">
		<? if($userOnline): ?>
			<div class="user-online"></div>
		<? endif; ?>
		<div class="user-avatar"><?=$osimo->getUserAvatar($post['poster_id']); ?></div>
		<div class="user-info">
			<h4><a href="profile.php?id=<?=$post['poster_id']?>" <? if($userColor){ echo "style=\"color:$userColor;\""; } ?>><?=$post['poster_username']?></a></h4>
			<br />
			<h4>User Info</h4>
			<p>Posts: <?=$userdata['posts']?></p>
			<p>Joined: <?=date('D M j, Y g:ia',$osimo->getUserTime($userdata['time_joined']));?></p>
			<p>Warnings: <?=$warnings['num_warnings']?>
			<? if($warnings['active_warnings']): ?>(<?=$warnings['active_warnings']?> recent)<? endif ?></p>
			<? if($osimo->userIsAdmin()||$osimo->userIsModerator()): ?>
			<p>IP: <?=$userdata['ip_address']?></p>
			<? endif; ?>
			<br />
			<h4>Post Info</h4>
			<p><a href="<?=$osimo->getPostPermalink($post['id'],$threadID,$page);?>"><img src="<?=THEMEPATH?>img/icons/link.png" alt="permalink" title="permalink" />&nbsp;Post #<?=$post['id']?></a></p>
			<p><img src="<?=THEMEPATH?>img/icons/time.png" />&nbsp;Posted: <?=date('D M j, Y g:ia',$osimo->getUserTime($post['post_time']));?></p>
		</div>
	</div>
	<div class="post-action-bar">
		<? if($postPreview): ?>
			<div class="post-preview-msg"><p><img src="<?=THEMEPATH?>img/icons/error.png" />&nbsp;This is a post preview</p></div>
		<? endif; ?>
		<? if(in_array($post['id'],$warnings['warn_posts'])): ?>
			<div class="post-preview-msg"><p><img src="<?=THEMEPATH?>img/icons/error.png" />&nbsp;User was warned for this post.</p></div>
		<? endif; ?>
		<? if(($osimo->userIsAdmin()||$osimo->userIsModerator())&&!$postPreview): ?>
			<a href="javascript:deletePost(<?=$post['id']?>)"><img src="<?=THEMEPATH?>img/delete_button.png" alt="delete" /></a>
			<a href="javascript:warnUser('<?=$post['poster_username']?>',<?=$post['id']?>)"><img src="<?=THEMEPATH?>img/warn_button.png" alt="warn" /></a>
		<? endif; ?>
		<? if(($osimo->userCanPostReply($parentForum)&&!$osimo->isThreadLocked($threadID))&&!$postPreview): ?>
			<a href="javascript:quotePost(<?=$post['id']?>)"><img src="<?=THEMEPATH?>img/quote_button.png" alt="quote" /></a>
		<? endif; ?>
		<? if(!$postPreview): ?>
			<?=$osimo->getEditPostLink($post['id'],'<img src="'.THEMEPATH.'img/edit_button.png" />');?>
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