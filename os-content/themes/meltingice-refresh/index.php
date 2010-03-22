<?php
/* Include the header */
include_once(THEMEPATH.'header.php');
?>

<div id="main-content">
<?php
if($osimo->areForums($forums))
{
	/*
	*	Next we need to get the categories, and we do so
	*	by using the getCategories() function.  This gives us the ID's
	*	of the categories.
	*/
	$categories = $osimo->getCategories($forums);
	
	/* The first loop that goes through each category */	
	foreach($categories as $category)
	{
		echo "<div class=\"category-container\">\n";
		?>
		<div class="category-header">
			<div class="category-desc">
				<h4><?=$osimo->getCategoryName('id='.$category);?></h4>
			</div>
			<div class="category-num">
				<p>Threads</p>
			</div>
			<div class="category-num">
				<p>Posts</p>
			</div>
			<div class="category-num">
				<p>Views</p>
			</div>
			<div class="category-lastpost">
				<p>Last post</p>
			</div>
		</div>
		
		<?php
			if($osimo->areForums($forums[$category]))
			{
				echo "<div class=\"forum-container\">\n";
				/* The second loop which goes through each category and lists the forums within that category */
				foreach($forums[$category] as $forum)
				{
					$userColor1 = $osimo->getUsernameColor($forum['last_poster_id']);
					?>
					<div class="list-item">
						<div class="forum-desc">
							<p class="forum-title"><?=$osimo->getForumLink($forum['id'],$forum['title']);?>
							<?php if($forum['new']): ?>
								&nbsp;<img src="<?php echo THEMEPATH; ?>img/icons/new.png" alt="new" />
							<?php endif; ?> 
							<br /><span class="forum-description"><?php echo $forum['description']; ?></span></p>
						</div>
						<div class="forum-threads">
							<p><?=$forum['threads']?></p>
						</div>
						<div class="forum-posts">
							<p><?=$forum['posts']?></p>
						</div>
						<div class="forum-views">
							<p><?=$forum['views']?></p>
						</div>
						<div class="forum-lastpost">
							<p>by <?php echo "<a href=\"profile.php?id={$forum['last_poster_id']}\"";
							if($userColor1){ echo "style=\"color:$userColor1;\""; }
							echo ">{$forum['last_poster']}</a>"; ?><br />
							<?php echo adodb_date('D M j, Y g:ia',$osimo->getUserTime($forum['last_post_time'])); ?></p>
						</div>
					</div>
					<?php
				}
				echo "</div>\n";
			}
		echo "</div>\n";
	}

}

$recent = $osimo->getRecentlyUpdatedThreads(6);
if(is_array($recent))
{
	echo "<div class=\"category-container\">\n";
		?>
	<div class="category-header">
	    <div class="category-desc">
	    	<h4>Recently Updated Threads</h4>
	    </div>
	    <div class="category-num">
	    	<p>Posts</p>
	    </div>
	    <div class="category-num">
	    	<p>Views</p>
	    </div>
	    <div class="category-lastpost">
	    	<p>Last post</p>
	    </div>
	</div>
	<div class="forum-container">
	<?
	foreach($recent as $threadID=>$thread)
	{ 
		$thisForum = $osimo->getThreadForum($threadID);
	?>
		<div class="list-item">
			<div class="forum-desc">
			    <p class="forum-title"><?=$osimo->getThreadLink($threadID,$thread['title']);?> 
			    <br /><span class="forum-description">Forum: <?=$osimo->getForumLink($thisForum['id'],$thisForum['title']);?></span></p>
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
	<?php }
	echo "</div></div>\n";
}

$onlineUsers = $osimo->getOnlineUsers();
if($onlineUsers):
?>
<div id="online_users">
	<h4><?php echo count($onlineUsers); ?> User(s) are currently online</h4>
	<p>
	<?php
	$i=0;
	foreach($onlineUsers as $id=>$username)
	{
		$userColor = $osimo->getUsernameColor($id);
		echo "<a href=\"profile.php?id=$id\"";
		if($userColor){ echo "style=\"color:$userColor;\""; }
		echo ">$username</a>";
		if($i<(count($onlineUsers)-1))
		{
			echo ", ";
		}
		$i++;
	}
	?>
	</p>
</div>
<?php endif; ?>

</div>

<?php
/* Include the Footer */
include_once(THEMEPATH.'footer.php');
?>