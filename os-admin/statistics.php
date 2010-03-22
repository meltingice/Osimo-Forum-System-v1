<?php
	include('header.php');
	include(ABS_ADMIN_INCLUDES.'ajax/statistics.php');
?>
<div id="navigation2">
	    <ul>
	        <li onclick="window.location='statistics.php?page=usage'">Site Usage</li>
	        <li onclick="window.location='statistics.php?page=syslog';">System Log</li>
	    </ul>
</div>
<div id="loading-wrap"><div id="loading" style="display:none"><img src="img/ajax-loader.gif" alt="Loading..." /></div></div>
<?php if($_GET['page']=='syslog'): ?>
<div id="content" class="clearfix">
	<h3>System Log</h3>
	
	<div id="system-log-wrap">
		<ul id="system-log">
			<?php $admin->readFromSysLog(time()-604800,time()); ?>
		</ul>
	</div>
	
	<div id="stat-controls">
		<div class="user-content-box">
			<h4>Date Range</h4>
			<form action="#" method="post">
				<table>
					<tr>
						<td><p>Start date:</p></td>
						<td><p><input type="text" value="<?php echo date('F j,Y',time()-604800); ?>" id="start-date" /></p></td>
					</tr>
					<tr>
						<td><p>End date:</p></td>
						<td><p><input type="text" value="<?php echo date('F j,Y'); ?>" id="end-date" /></p></td>
					</tr>
				</table>
				<p><input type="button" value="Update" onclick="setLogDates()" /></p>
			</form>
		</div>
	</div>

</div>
<?php else: ?>
<div id="content" class="clearfix">
	<h3>Statistics</h3>
	
	<div id="stat-info-wrap">
		<script>
			outputGraph('all','views');
		</script>
	</div>
	
	<div id="stat-controls">
		<div class="user-content-box">
			<h4>Content Control</h4>
			<form action="#" method="post">
				<table>
					<tr>
						<td><p>Data type:</p></td>
						<td>
							<select id="stat-data-type" onchange="statCheckDataType()">
								<option value="views" selected>Views</option>
								<option value="posts">New Posts</option>
								<option value="threads">New Threads</option>
								<option value="newuser">Registrations</option>
							</select>
						</td>
					</tr>
				</table>
				<table id="stat-forum-table">
					<tr>
						<td><p>Forum:</p></td>
						<td>
							<select id="stat-data-forum">
								<option value="all" selected>All Forums</option>
								<?php
									$forums = $admin->getForumNames();
									if(is_array($forums))
									{
										foreach($forums as $forum)
										{
											echo "<option value=\"{$forum['id']}\">{$forum['title']}</option>\n";
										}
									}
								?>
							</select>
						</td>
					</tr>
				</table>
				<p><input type="button" value="Update" onclick="setGraphData()" /></p>
			</form>
		</div>
		<div class="user-content-box">
			<h4>Date Range</h4>
			<form action="#" method="post">
				<table>
					<tr>
						<td><p>Start date:</p></td>
						<td><p><input type="text" value="<?php echo date('F j,Y',time()-604800); ?>" id="start-date" /></p></td>
					</tr>
					<tr>
						<td><p>End date:</p></td>
						<td><p><input type="text" value="<?php echo date('F j,Y'); ?>" id="end-date" /></p></td>
					</tr>
				</table>
				<p><input type="button" value="Update" onclick="setGraphDates()" /></p>
			</form>
		</div>
	</div>
</div>

<script>
	$("#start-date").datepicker({ 
    	dateFormat: "MM d, yy"
	});
	$("#end-date").datepicker({
		dateFormat: "MM d, yy"
	});
</script>
<?php endif; ?>

<?php include('footer.php'); ?>