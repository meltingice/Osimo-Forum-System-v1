<div class="list-item">
	<div class="forum-desc">
	    <p class="forum-title"><?=$osimo->getMessageLink($message['id'],$message['title']);?>
	    <?php if($message['new']): ?>
	    	&nbsp;<img src="<?=THEMEPATH?>img/icons/new.png" alt="new" />
	    <? endif; ?>				
	    <br /><span class="forum-description">Created <?=adodb_date('n/j/Y g:ia',$osimo->getUserTime($message['time_created']));?></span></p>
	</div>
	<div class="forum-posts">
	    <p><?=$message['posts']?></p>
	</div>
	<div class="forum-views">
	    <p><?=$message['read_status']?></p>
	</div>
	<div class="forum-lastpost">
		<?
			$sentColor = $osimo->getUsernameColor($message['user_sent']['ID']);
			$userColor2 = $osimo->getUsernameColor($message['user_sent']['last_poster_id']);
		?>
	    <p>Sent by <?php echo "<a href=\"profile.php?id={$message['user_sent']['ID']}\"";
	    if($sentColor){ echo "style=\"color:$sentColor;\""; }
	    echo ">{$message['user_sent']['name']}</a>"; ?>
	    <br />
	    Last reply by <?php echo "<a href=\"profile.php?id={$message['last_poster_id']}\"";
	    if($userColor2){ echo "style=\"color:$userColor2;\""; }
	    echo ">{$message['last_poster']}</a>"; ?> at <?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($message['last_post_time'])); ?></p>
	</div>
</div>