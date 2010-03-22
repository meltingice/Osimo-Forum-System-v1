<?php
$messages = $osimo->getPrivateMessageThreads('inbox');
/* Include the header */
include_once(THEMEPATH.'header.php');
?>
<div id="main-content">
<?php
$user = $osimo->getLoggedInUser();
echo "<div id=\"breadcrumb_trail\"><h4>Private Messages for {$user['name']}</h4></div>";
?>
<div id="post-actions" style="padding-right: 10px;"><a href="javascript:toggleMessageCompose()">[+] Compose Message</a> &bull; <a href="javascript:showInboxMessages()" onclick="updateMessageType('inbox');">Inbox</a> &bull; <a href="javascript:showSentMessages()" onclick="updateMessageType('sent');">Sent Messages</a></div>

<? if($osimo->userCanSendPM()): ?>
<div id="new-message" style="display:none">
	<form id="osimo_newPM" action="#">
		<p class="input-label">Recipient</p>
		<p><input type="text" id="osimo_recipient" class="osimo_usernamesearch" style="width: 400px;" /></p>
		<p class="input-label">Subject</p>
		<p><input type="text" id="osimo_messagesubj" style="width: 500px;" /></p>
		<p class="input-label">Message</p>
		<textarea id="osimo_messagecontent"></textarea>
	    <p style="padding-top: 5px;margin-left: 10px;"><input type="button" value="Send" onclick="newMessageThread();updateMessageType('sent');toggleMessageCompose();" style="margin-left: 10px;" /><input type="button" value="BBCode Help" onclick="BBHelp()" /></p>
	</form>
</div>
<? endif; ?>
	
<div id="messages-wrap" class="clearfix">
	<div class="category-header">
		<div class="category-desc">
		    <h4 id="message-type">Inbox</h4>
		</div>
		<div class="category-num">
		    <p>Posts</p>
		</div>
		<div class="category-num">
		    <p>Status</p>
		</div>
		<div class="category-lastpost">
		    <p>Information</p>
		</div>
	</div>
	<?php
	echo "<div id=\"osimo_messages\">";
	if($osimo->areMessages($messages))
	{
		foreach($messages as $message)
		{
			include(THEMEPATH."singlemessagethread.php");
		}
	}
	else
	{
		echo "<p id=\"no-messages\">No Messages</p>";
	}
	echo "</div>";
	?>
</div>

</div>

<?php include(THEMEPATH.'footer.php'); ?>