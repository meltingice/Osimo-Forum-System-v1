<?php
/* Include the header */
include_once(THEMEPATH.'header.php');

$user = $osimo->getLoggedInUser();
$title = $osimo->getMessageTitle();
$users = $osimo->getMessageUsers($threadID);
?>
<div id="main-content">
	<?php
	echo "<div id=\"breadcrumb_trail\" style=\"margin-bottom: 10px\"><h4><a href=\"messages.php\">Inbox</a> > $title [<a href=\"profile.php?id={$users['user_sent']['ID']}\">{$users['user_sent']['name']}</a> & <a href=\"profile.php?id={$users['user_received']['ID']}\">{$users['user_received']['name']}</a>]</h4></div>";
	
	echo "<div id=\"osimo_messageposts\">";
	foreach($posts as $post)
	{
		include(THEMEPATH.'singlemessage.php');
	}
	echo "</div>";
	?>
	
	<?php if($osimo->userCanSendPM()): ?>
	
	<h4 id="postpreview_title" style="display:none">Post Preview</h4>
	<div id="osimo_postpreview" style="display:none"></div>
	
	<form id="osimo_newmessageform" action="javascript:newMessagePost(<?php echo $threadID; ?>)">
		<h4>Reply to User</h4>
		<textarea id="osimo_messagepost"></textarea>
		<p id="post-buttons"><input type="submit" value="Post" onclick="$('#postpreview_title').fadeOut('normal');" /><input type="button" value="Preview" onclick="postPreview();showPostPreview();" /><input type="button" value="BBCode Help" onclick="BBHelp()" /></p>
	</form>
</form>
	
	<?php endif; ?>
</div>