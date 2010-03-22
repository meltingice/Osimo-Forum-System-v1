<div class="list-item">
	<div class="forum-desc">
	    <p class="forum-title"><?=$osimo->getThreadLink($thread['id'],$thread['title']);?>
	    <?php if($thread['new']): ?>
	    	&nbsp;<img src="<?=THEMEPATH?>img/icons/new.png" alt="new" />
	    <?php
	    	endif;
	    	if($thread['locked']): 
	    ?>				
	    	&nbsp;<img src="<?=THEMEPATH?>img/icons/lock.png" alt="locked" />
	    <?php
	    	endif;
	    	if($thread['sticky']):
	    ?>				
	    	&nbsp;<img src="<?=THEMEPATH?>img/icons/star.png" alt="sticky" />
	    <?php endif; ?>
	    <br /><span class="forum-description"><?=$thread['description']?></span></p>
	</div>
	<div class="forum-posts">
	    <p><?=$thread['posts']?></p>
	</div>
	<div class="forum-views">
	    <p><?=$thread['views']?></p>
	</div>
	<div class="forum-lastpost">
	    <p>by <?php echo "<a href=\"profile.php?id={$thread['last_poster_id']}\">{$thread['last_poster']}</a>"; ?><br />
	    <?php echo adodb_date('D M j, Y g:ia',$osimo->getUserTime($thread['last_post_time'])); ?></p>
	</div>
</div>