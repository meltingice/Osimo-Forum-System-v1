<?php
/* Include the header */
include_once(THEMEPATH.'header.php');

include_once(THEMEPATH.'navbar.php');

/* 
*	Gets the forum that this thread is in.
*	$forum is an array consisting of $forum['id'] and $forum['title']
*/
$forum = $osimo->getThreadForum($threadID);
?>
<div id="main-content">
<?php
if($osimo->arePosts($posts))
{
	$breadcrumbs = $osimo->getBreadcrumbTrail('thread');
	echo "<div id=\"breadcrumb_trail\"><h4>";
	echo "<a href=\"index.php\">Home</a> > ";
	$i=0;
	foreach($breadcrumbs as $crumb)
	{
		echo "<a href=\"forum.php?id={$crumb['id']}\">{$crumb['name']}</a>";
		echo " > ";
	}
	echo "$threadName</h4></div>";
	?>
	<div id="post-actions"><img src="<?php echo THEMEPATH; ?>img/icons/arrow_refresh.png" alt="refresh" />&nbsp;<a href="javascript:refreshPosts(<?php echo $threadID; ?>)">Refresh Posts</a>
	<?php
		/* Administrative options */
		if($osimo->userIsModerator()||$osimo->userIsAdmin())
		{
			echo " <span style=\"color: #f2f2f2;\">|</span> <a href=\"#\" onclick=\"deleteThread($threadID)\">Delete Thread</a>"; 
			if($osimo->isThreadSticky($threadID)){ echo " <span style=\"color: #f2f2f2;\">|</span> <a href=\"#\" onclick=\"stickyThread($threadID, false)\">Unsticky Thread</a>"; }
			else{ echo " <span style=\"color: #f2f2f2;\">|</span> <a href=\"#\" onclick=\"stickyThread($threadID,true)\">Sticky Thread</a>"; }
			if($osimo->isThreadLocked($threadID)){ echo " <span style=\"color: #f2f2f2;\">|</span> <img src=\"".THEMEPATH."img/icons/lock_edit.png\" alt=\"lock\" />&nbsp;<a href=\"#\" onclick=\"lockThread($threadID, false)\">Unlock Thread</a>"; }
			else{ echo " <span style=\"color: #f2f2f2;\">|</span> <img src=\"".THEMEPATH."img/icons/lock_edit.png\" alt=\"lock\" />&nbsp;<a href=\"#\" onclick=\"lockThread($threadID,true)\">Lock Thread</a>"; }
		}
		else
		{
			if($osimo->isThreadLocked($threadID)){ echo " <span style=\"color: #f2f2f2;\">|</span> <span style=\"color: #c4c4c4\">Thread Locked</span>"; }
		} /* End administrative options */

		/* Pagination */
		echo "<span id=\"thread-nav\"> | Page: <span id=\"osimo_pagination\">";
		echo $osimo->getPresetPagination('thread',$threadID);
		echo "</span>&nbsp;</span>";	
		/* End pagination */	
		?>
	</div>
	
	<?php
	echo "<div id=\"osimo_posts\" class=\"post-container\">";
	
	/* Output the posts */
	foreach($posts as $post)
	{
		include(THEMEPATH.'singlepost.php');
	}
	echo "</div>";
}
?>
<div id="post-nav">
	<div class="prev-post"><input type="button" value="&lt; Prev" onclick="prevPostPage(<?php echo $threadID; ?>);returnToTop();" /></div>
	<div class="top-post"><input type="button" value="Return to Top" onclick="returnToTop()" /></div>
	<div class="next-post"><input type="button" value="Next &gt;" onclick="nextPostPage(<?php echo $threadID; ?>);returnToTop();" /></div>
</div>
<?php if($osimo->userCanPostReply($parentForum)&&!$osimo->isThreadLocked($threadID)): ?>

<h4 id="postpreview_title" style="display:none">Post Preview</h4>
<div id="osimo_postpreview" style="display:none"></div>

<form id="osimo_postform" action="javascript:submitPost(<?php echo $threadID; ?>)">
	<h4>Reply to Thread</h4>
	<textarea id="osimo_postbox"></textarea>
	<p id="post-buttons"><input type="submit" value="Post" onclick="$('#postpreview_title').fadeOut('normal');" /><input type="button" value="Preview" onclick="postPreview();showPostPreview();" /><input type="button" value="BBCode Help" onclick="BBHelp()" /></p>
</form>
<?php endif;

$onlineUsers = $osimo->getOnlineUsers('thread',$threadID);
if($onlineUsers):
?>
<div id="online_users" class="online_users_thread">
	<h4><?php echo count($onlineUsers); ?> User(s) are reading this thread</h4>
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