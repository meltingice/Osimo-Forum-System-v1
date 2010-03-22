<?php 
/*
*	This might seem a bit strange at first, but this allows us to use ajax easily
*	Remember, this file is loaded "in the loop" for each post
*/

$sig = $osimo->getUserSignature($post['poster_id']);
$userdata = $osimo->getUserInfo($post['poster_id'],'posts');
?>
<div class="forum-post">
	<div class="post-info">
		<div class="user-avatar"><?php echo $osimo->getUserAvatar($post['poster_id']); ?></div><div class="user-info"><p><a href="profile.php?id=<?php echo $post['poster_id'];?>"><?php echo $post['poster_username']; ?></a></p>
		<p><small><?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($post['post_time'])); ?></small></p>
		<p><small>Posts: <?php echo $userdata['posts']; ?></small></p></div>
		<p class="edit-post-link"><a href="javascript:quotePost(<?php echo $post['id'].",'".$post['poster_username']."'"; ?>)">Quote</a></p>
		<p class="edit-post-link"><?php echo $osimo->getEditPostLink($post['id'],'Edit Post'); ?></p>
		<?php if($osimo->userIsModerator()||$osimo->userIsAdmin()): ?>
		<p class="edit-post-link"><a href="javascript:deletePost(<?php echo $post['id']; ?>)">Delete Post</a></p>
		<?php endif; ?>
	</div>
	<div class="post-content">
		<div class="post-body" <?php if(!$sig){ echo "style=\"padding-bottom:20px\""; } ?>>
			<div id="post-<?php echo $post['id']; ?>" class="post"><?php echo $post['body']; ?></div>
		</div>
		<?php if($sig): ?>
		<div class="sig-wrap">
			<p>------------------</p>
			<p><?php echo $sig; ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>