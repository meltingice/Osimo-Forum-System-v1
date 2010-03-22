<?php include('header.php'); ?>
<div id="navigation2">
	    <ul>
	        <li onclick="loadPage('manageusers'); window.location='#manageusers';">Manage Users</li>
	        <li onclick="loadPage('banlist'); window.location='#banlist'">Banlist</li>
	        <li onclick="loadPage('usersearch'); window.location='#usersearch'">User Search</li>
	    </ul>
</div>
<div id="loading-wrap"><div id="loading" style="display:none"><img src="img/ajax-loader.gif" alt="Loading..." /></div></div>
<div id="page-content">
	<script>
		var bookmark = ajaxBookmark();
		if(!bookmark){ loadPage('manageusers'); }
		else{ loadPage(bookmark); }
	</script>
</div>
<?php include('footer.php'); ?>