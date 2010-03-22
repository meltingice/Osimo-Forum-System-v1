<?php
/* Include the header */
include_once(THEMEPATH.'header.php');

include_once(THEMEPATH.'navbar.php');
?>
<div id="main-content">
	<div id="search-box">
		<form action="os-includes/search.php" method="post">
			<p class="input-field"><input type="text" <? if($_GET['type']=='postsby'){ echo "class=\"osimo_usernamesearch\""; } ?> value='<?php if($_GET['q']){ echo $_GET['q']; } ?>' name="osimo_search_query" id="osimo_search_query" /></p>
			<p style="margin-left: 10px !important;">Search for: <select name="osimo_search_type" id="osimo_search_type" onchange="setOsimoSearchQuery()">
				<option value="content" <?php if($_GET['type']=='content'){ echo "selected"; } ?>>Posts &amp; Threads</option>
				<option value="postsby" <?php if($_GET['type']=='postsby'){ echo "selected"; } ?>>Posts by User</option>
			</select>
			<input type="submit" value="search" class="submit-button" />
		</form>
	</div>
<?
if($osimo->isSearch($search) == 'content')
{
	echo "<div class=\"category-container\" style=\"margin-top: 20px;\">";
	?>
	<div class="category-header">
	    <div class="category-desc">
	    	<h4>Search Results</h4>
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
	echo "<div id=\"page_nav\"><p>Page: <span id=\"osimo_pagination\">";
	$osimo->getSearchPresetPagination($numSearchPages);
	echo "</span></p></div>";
	
	echo "<div id=\"osimo_threads\" class=\"thread-container\">";
	foreach($search as $threadID=>$data)
	{
		?>
		<div class="list-item">
			<div class="forum-desc">
			    <p class="forum-title"><?=$osimo->getThreadLink($threadID,$data['title']);?>
			    <?php if($data['new']): ?>
			    	&nbsp;<img src="<?=THEMEPATH?>img/icons/new.png" alt="new" />
			    <?php
			    	endif;
			    	if($data['locked']): 
			    ?>				
			    	&nbsp;<img src="<?=THEMEPATH?>img/icons/lock.png" alt="locked" />
			    <?php
			    	endif;
			    	if($data['sticky']):
			    ?>				
			    	&nbsp;<img src="<?=THEMEPATH?>img/icons/star.png" alt="sticky" />
			    <?php endif; ?>
			    <br /><span class="forum-description"><?=$data['description']?></span></p>
			</div>
			<div class="forum-posts">
			    <p><?=$data['posts']?></p>
			</div>
			<div class="forum-views">
			    <p><?=$data['views']?></p>
			</div>
			<div class="forum-lastpost">
			    <p>by <?php echo "<a href=\"profile.php?id={$data['last_poster_id']}\">{$data['last_poster']}</a>"; ?><br />
			    <?php echo adodb_date('D M j, Y g:ia',$osimo->getUserTime($data['last_post_time'])); ?></p>
			</div>
		</div>
		<?
	}
	echo "</div></div>";
}
elseif($osimo->isSearch($search) == 'postsby')
{
	echo "<div id=\"osimo_posts\" class=\"post-container\" style=\"margin-top:10px\">";
	echo "<div id=\"user-search-info\">";
		echo "<h4>Posts by ".$_GET['q']."</h4>";
		echo "<div id=\"page_nav\"><p>Page: <span id=\"osimo_pagination\">";
		$osimo->getSearchPresetPagination($numSearchPages);
		echo "</span></p></div>";
	echo "</div>";
	foreach($search as $threadID=>$thread)
	{
		$threadName = $osimo->getThreadName($threadID);
		echo "<h4 class=\"search-thread\">".$osimo->getThreadLink($threadID,$threadName)."</h4>";
		echo "<div class=\"user-post-search\">";
		foreach($thread as $post)
		{
			echo "<div class=\"post-search\">";
			echo "<div class=\"post-search-info\">";
			echo "<p>#{$post['id']} - ".adodb_date('n/j/Y g:ia',$osimo->getUserTime($post['post_time']))."</p>";
			echo "</div>";
			echo $post['body'];
			echo "</div>";
		}
		echo "</div>";
	}
	echo "</div>";
}
elseif($osimo->isSearch($search) == 'noresults')
{
	echo "<div id=\"search-start\">";
	echo "<h4>osimo forum search</h4>";
	echo "<p>no results matched your query</p>";
	echo "</div>";
}
elseif($osimo->isSearch($search) == 'tooshort')
{
	echo "<div id=\"search-start\">";
	echo "<h4>osimo forum search</h4>";
	echo "<p>your search query must be at least 3 characters</p>";
	echo "</div>";
}
else
{
	echo "<div id=\"search-start\">";
	echo "<h4>osimo forum search</h4>";
	echo "<p>enter a search query into the search box to begin</p>";
	echo "</div>";
}
?>
</div>