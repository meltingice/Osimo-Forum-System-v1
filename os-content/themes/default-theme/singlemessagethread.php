<div class="list-item">
	<p class="forum-title">
	<?php echo $osimo->getMessageLink($message['id'],$message['title']); ?>
	<?php if($message['new']): ?>
		&nbsp;<img src="<?php echo THEMEPATH; ?>img/icons/new.png" alt="new" />
	<?php endif; ?>
	<br /><span class="forum-description">Created at <?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($message['time_created'])); ?></span></p>
	<p class="forum-data">
		Sent by <?php echo "<a href=\"profile.php?id={$message['user_sent']['ID']}\">{$message['user_sent']['name']}</a>"; ?><br />
	    Last reply by <?php echo "<a href=\"profile.php?id={$message['last_poster_id']}\">{$message['last_poster']}</a>"; ?> at <?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($message['last_post_time'])); ?><br />
	    Message status: <?php echo ucfirst($message['read_status']); ?><br />
	    Posts: <?php echo $message['posts']; ?>
	</p>
</div>