<?php
$sig = $osimo->getUserSignature($post['poster']);
$userdata = $osimo->getUserInfo($post['poster'],'posts');
?>

<div class="forum-post">
	<div id="message-<?php echo $post['id']; ?>" class="post-info">
		<div class="user-avatar"><?php echo $osimo->getUserAvatar($post['poster']); ?></div><div class="user-info"><p><a href="profile.php?id=<?php echo $post['poster'];?>"><?php echo $post['poster_username']; ?></a></p>
		<p><small><?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($post['post_time'])); ?></small></p>
		<p><small>Posts: <?php echo $userdata['posts']; ?></small></p></div>
	</div>
	<div class="post-content">
		<div class="post-body">
			<p><?php echo $post['body']; ?></p>
		</div>
		<?php if($sig): ?>
		<div class="sig-wrap">
			<p>------------------</p>
			<p><?php echo $sig; ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>