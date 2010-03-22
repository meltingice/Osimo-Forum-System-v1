<?php
$messages = $osimo->getPrivateMessageThreads('inbox');
/* Include the header */
include_once(THEMEPATH.'header.php');
?>
<div id="main-content">
<?php
$user = $osimo->getLoggedInUser();
echo "<div id=\"breadcrumb_trail\"><h4 style=\"color: #e6e6e6\">Private Messages for {$user['name']}</h4></div>";
?>
<div id="post-actions" style="padding-right: 10px;"><a href="javascript:toggleMessageCompose()">[+] Compose Message</a> | <a href="javascript:showInboxMessages()" onclick="updateMessageType('inbox');">Inbox</a> | <a href="javascript:showSentMessages()" onclick="updateMessageType('sent');">Sent Messages</a></div>

<div id="messages-wrap" class="clearfix">
	<div id="new-message" style="display:none">
		<form action="#" id="osimo_newPM">
			<p class="input-label">Recipient:</p>
			<p><input type="text" id="osimo_recipient" class="osimo_usernamesearch" /></p>
			<p class="input-label">Subject:</p>
			<p><input type="text" id="osimo_messagesubj" /></p>
			<p class="input-label">Message:</p></td>
			<p><textarea id="osimo_messagecontent" /></textarea></p>
			<p id="message-send"><input type="button" value="Send" onclick="newMessageThread();updateMessageType('sent');toggleMessageCompose();" /></p>
		</form>
	</div>
	
	<h4 id="message-type">Inbox</h4>
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