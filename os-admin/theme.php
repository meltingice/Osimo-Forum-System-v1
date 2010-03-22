<?php 
	include('header.php');
?>
<div id="navigation2">
	    <ul>
	        <li onclick="loadPage('active'); window.location='#active';">Set Active Theme</li>
	        <li onclick="loadPage('editor'); window.location='#editor'">Theme Editor</li>
	        <li onclick="loadPage('smilies'); window.location='#smilies'">Smilies</li>
	    </ul>
</div>
<div id="loading-wrap"><div id="loading" style="display:none"><img src="img/ajax-loader.gif" alt="Loading..." /></div></div>
<div id="alert-msg" style="display:none"><p id="alert-msg-text"></p></div>
<div id="page-content">
	<script>
		var bookmark = ajaxBookmark();
		if(!bookmark){ loadPage('active'); }
		else{ loadPage(bookmark); }
	</script>
</div>

<?php include('footer.php'); ?>