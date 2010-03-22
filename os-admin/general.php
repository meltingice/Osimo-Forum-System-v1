<?php
	include('header.php');
?>
<div id="navigation2">
	    <ul>
	        <li onclick="loadPage('siteinfo'); window.location='#upgrade';">Site Information</li>
	        <li onclick="loadPage('upgrade'); window.location='#upgrade'">Automatic Upgrade</li>
	    </ul>
</div>
<div id="loading-wrap"><div id="loading" style="display:none"><img src="img/ajax-loader.gif" alt="Loading..." /></div></div>
<div id="alert-msg" style="display:none"><p id="alert-msg-text"></p></div>
<div id="page-content">
	<script>
		var bookmark = ajaxBookmark();
		if(!bookmark){ loadPage('siteinfo'); }
		else{ loadPage(bookmark); }
	</script>
</div>
<?php include('footer.php'); ?>