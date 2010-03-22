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

}?>


<div class="category-container" style="padding-bottom: 5px;">
<?php
echo "<h4>Threads";
if($osimo->userCanPostThread($forumID))
{
	echo " <span id=\"new-thread-link\">| <a href=\"javascript:showNewThreadForm()\">new thread [+]</a></span>";
}
echo "</h4>";

/* Pagination */
echo "<div id=\"page_nav\"><p>Page: <span id=\"osimo_pagination\">";
echo $osimo->getPresetPagination('forum',$forumID);
echo "</span></p></div>";

if($osimo->userCanPostThread($forumID)):
?>
<form id="osimo_newthreadform" action="#" style="display:none">
	<table>
		<tr>
			<td><p>Title:</p></td>
			<td><p><input type="text" id="osimo_newthreadtitle" /></p></td>
		</tr>
		<tr>
			<td><p>Description:</p></td>
			<td><p><input type="text" id="osimo_newthreaddescription" /></p></td>
		</tr>
		<tr>
			<td><p>Post:</p></td>
			<td><textarea id="osimo_newthreadpost"></textarea></td>
		</tr>
	</table>
    <p><input type="button" value="Create" onclick="newThread(<?php echo $forumID; ?>);showNewThreadForm();" style="margin-left: 10px;" /></p>
</form>
<?php
endif;

echo "<div id=\"osimo_threads\" class=\"thread-container\">";
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
	<h4><?php echo count($onlineUsers); ?> User(s) are browsing this forum</h4>
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