<div class="list-item" <?php if($thread['sticky']){ echo "style=\"background-color: #f1f1f1 !important; border: 1px #b6b6b6 solid;\""; } ?>>
	<p class="forum-title">
	<?php echo $osimo->getThreadLink($thread['id'],$thread['title']); ?>
	
	<?php if($thread['new']): ?>
		&nbsp;<img src="<?php echo THEMEPATH; ?>img/icons/new.png" alt="new" />
	<?php 
		endif;
		if($thread['locked']): 
	?>
		&nbsp;<img src="<?php echo THEMEPATH; ?>img/icons/lock.png" alt="locked" />
	<?php
		endif;
		if($thread['sticky']):
	?>
		&nbsp;<img src="<?php echo THEMEPATH; ?>img/icons/star.png" alt="sticky" />
	<?php endif; ?>
	
	<br /><span class="forum-description"><?php echo $thread['description']; ?></span></p>
	<p class="forum-data">
	    Last post by <?php echo "<a href=\"profile.php?id={$thread['last_poster_id']}\">{$thread['last_poster']}</a>"; ?> at <?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($thread['last_post_time'])); ?><br />
	    Created by <?php echo "<a href=\"profile.php?id={$thread['original_poster_id']}\">{$thread['original_poster']}</a>"; ?> at <?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($thread['original_post_time'])); ?><br />
	    Posts: <?php echo $thread['posts']; ?><br />
	    Views: <?php echo $thread['views']; ?>
	</p>
</div>