<?php
	$modpage = true;
	include('header.php');
	$newestUser = $osimo->getNewestUser();
?>
<div id="loading-wrap"><div id="loading" style="display:none"><img src="img/ajax-loader.gif" alt="Loading..." /></div></div>
<div id="content" class="clearfix">
	<div id="welcome-info">
		<h3>Welcome back, <span style="color: #51a0d8"><?php echo $_SESSION['admin']['name']; ?></span></h3>
		
		<div class="welcome-content-box">
			<h4>Quick Overview</h4>
			<p>Today so far there have been...</p>
			<div id="today-stats">
				<?php $admin->outputTodayStats(); ?>
			</div>
		</div>
		
		<div class="welcome-content-box">
			<h4>Recently Updated Threads</h4>
			<ul id="recent-threads">
				<?php
				$recentThreads = $osimo->getRecentlyUpdatedThreads(6);
				if(is_array($recentThreads))
				{
					foreach($recentThreads as $id=>$thread)
					{
						echo "<li><span class=\"recent-thread-title\"><a href=\"../thread.php?id=$id\">{$thread['title']}</a></span><br />";
						echo "<span class=\"recent-thread-info\">- created by <a href=\"user.php#profile={$thread['original_poster_id']}\">{$thread['original_poster']}</a>, last post by <a href=\"user.php#profile={$thread['last_poster_id']}\">{$thread['last_poster']}</a> @ ";
						echo date('n/j/Y g:ia',$thread['last_post_time']);
						echo "</span></li>";
					}
				}
				?>
			</ul>
		</div>
	</div>

	<div id="home-statistics">
	    <h3>Current Statistics</h3>
	    
	    <div id="stats-wrap">
	    	<div id="latestUser">
	    	    <h4><?php echo $newestUser['name']; ?></h4>
	    	</div>  
	    	  
	    	<div id="onlineStats">    
	    		<div class="statpost">users online</div>
	    		<div class="statvalue">
	    		<?php
	    		$onlineUsers = $osimo->getOnlineUsers();
				if($onlineUsers){ echo count($onlineUsers); }
				else{ echo 0; }
				?>
	    		</div>
	    	</div>  
	    	  
	    	<div id="boardStats">
	    	    <span class="arrowLeft"><img src="img/arrowLeft.png" alt="ArrowLeft" height="25" width="18"></span>
	    	    <span class="arrowRight"><img src="img/arrowRight.png" alt="ArrowRight" height="25" width="18"></span>
	    	</div>   
	     </div>
	</div>
	
	<div class="welcome-content-box left-box">
		<?php $topUsers = $admin->getTopUsers(10); ?>
		<h4>Top Users</h4>
		<table id="top-users">
			<tr>
			<?php
				$i=1;
				foreach($topUsers as $id=>$user)
				{
					echo "<td class=\"user-ranking\">$i.</td>\n";
					echo "<td class=\"top-user\"><a href=\"user.php#profile=$id\">$user</a></td>\n";
					if($i%2==0&&$i!=10){ echo "</tr><tr>\n"; }
					$i++;
				}
			?>
			</tr>
		</table>
	</div>
</div>

<script>
$(window).ready(function(){
	$left = 0;
	$("#onlineStatRight").click(function(){
		$left = $left - 277;
		$("#stat").animate({left: $left+"px"})
	});
	
	initTodayPoller();
});
</script>

<?php include('footer.php'); ?>