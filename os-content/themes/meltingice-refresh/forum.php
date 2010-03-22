<?php
/* Include the header */
include_once(THEMEPATH.'header.php');

/* Retrieve the threads */
$threads = $osimo->getThreadList("forum=$forumID","page=$page","sticky=true");
?>

<div id="main-content">
<?php
/* Output breadcrumb trail */
$breadcrumbs = $osimo->getBreadcrumbTrail('forum');
echo "<div id=\"breadcrumb_trail\" style=\"margin-bottom: 10px\"><h4>";
echo "<a href=\"index.php\">Home</a> > ";
$i=0;
foreach($breadcrumbs as $crumb)
{
    echo "<a href=\"forum.php?id={$crumb['id']}\">{$crumb['name']}</a>";
    if($i<count($breadcrumbs)-1)
    {
    	echo " > ";
    }
    $i++;
}
echo "</h4></div>";

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

}?>


<?php
if($osimo->userCanPostThread($forumID)):
?>
<form id="osimo_newthreadform" action="#" style="display:none">
	<p class="input-label">Title</p>
	<p><input type="text" id="osimo_newthreadtitle" /></p>
	<p class="input-label">Description</p>
	<p><input type="text" id="osimo_newthreaddescription" /></p>
	<p class="input-label">Post</p>
	<textarea id="osimo_newthreadpost"></textarea>
    <p style="padding-top: 5px;margin-left: 10px;"><input type="button" value="Create" onclick="newThread(<?php echo $forumID; ?>);showNewThreadForm();" style="margin-left: 10px;" /><input type="button" value="BBCode Help" onclick="BBHelp()" /></p>
</form>
<?
endif;

echo "<div class=\"category-container\">\n";
		?>
	<div class="category-header">
	    <div class="category-desc">
	    	<h4>Threads
	    	<?
	    	if($osimo->userCanPostThread($forumID))
			{
				echo " <span id=\"new-thread-link\">| <a href=\"javascript:showNewThreadForm()\">new thread [+]</a></span>";
			}
	    	?></h4>
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
<?

/* Pagination */
echo "<div id=\"page_nav\"><p>Page: <span id=\"osimo_pagination\">";
echo $osimo->getPresetPagination('forum',$forumID);
echo "</span></p></div>";
?>
<div id="osimo_threads" class="forum-container">
<?
if($osimo->areThreads($threads))
{
	foreach($threads as $thread)
	{
		include(THEMEPATH.'singlethread.php');
	}
}
echo "</div>\n</div>";

$onlineUsers = $osimo->getOnlineUsers('forum',$forumID);
if($onlineUsers):
?>
<div id="online_users">
	<h4><?=count($onlineUsers);?> User(s) are browsing this forum</h4>
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