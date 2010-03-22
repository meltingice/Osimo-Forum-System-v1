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
		echo "<h4>".$osimo->getCategoryName('id='.$category)."</h4>";
		?>
		<?php /* Here we convert the category ID to its name */ ?>
		<?php
			if($osimo->areForums($forums[$category]))
			{
				echo "<div class=\"forum-container\">\n";
				/* The second loop which goes through each category and lists the forums within that category */
				foreach($forums[$category] as $forum)
				{
					?>
					<div class="list-item">
						<p class="forum-title"><?php echo $osimo->getForumLink($forum['id'],$forum['title']); ?>
						<?php if($forum['new']): ?>
							&nbsp;<img src="<?php echo THEMEPATH; ?>img/icons/new.png" alt="new" />
						<?php endif; ?> 
						<br /><span class="forum-description"><?php echo $forum['description']; ?></span></p>
						<p class="forum-data">
							Last post by <?php echo "<a href=\"profile.php?id={$forum['last_poster_id']}\">{$forum['last_poster']}</a>"; ?> at <?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($forum['last_post_time'])); ?><br />
							Threads: <?php echo $forum['threads']; ?><br />
							Posts: <?php echo $forum['posts']; ?><br />
							Views: <?php echo $forum['views']; ?>
						</p>
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
	echo "<div class=\"category-container thread-container\" style=\"padding-bottom: 5px;\">\n";
	echo "<h4 style=\"padding-bottom: 5px;\">Recently Updated Threads</h4>\n";
	foreach($recent as $threadID=>$thread)
	{ 
		$thisForum = $osimo->getThreadForum($threadID);
	?>
		<div class="list-item" style="height:40px">
			<p class="forum-title" style="margin-top: 4px !important">
			<?php echo $osimo->getThreadLink($threadID,$thread['title']); ?>
			<br /><span class="forum-description">Forum: <a href="forum.php?id=<?php echo $thisForum['id']; ?>"><?php echo $thisForum['title']; ?></a></span></p>
			<p class="forum-data" style="margin-top: 10px;">
			    Last post by <?php echo "<a href=\"profile.php?id={$thread['last_poster_id']}\">{$thread['last_poster']}</a>"; ?> at <?php echo adodb_date('n/j/Y g:ia',$osimo->getUserTime($thread['last_post_time'])); ?><br />
			</p>
		</div>
	<?php }
	echo "</div>\n";
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
		echo "<a href=\"profile.php?id=$id\">$username</a>";
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