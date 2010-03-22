<?php
session_start();
if(!isset($_SESSION['admin'])){ exit; }
if(!isset($osimo))
{
	include_once('../../../os-includes/dbconnect.php');
	include_once('../../../os-includes/paths.php');
	include_once('../../../os-includes/osimo.php');
	$osimo = new Osimo(); 
}
if(!isset($admin))
{
	include_once('../admin.php');
	$admin = new OsimoAdmin();
}

if(isset($_POST['loadPage'])){ loadPage($_POST['loadPage']); }

function loadPage($page)
{
	global $osimo,$admin;
	if($page=='active')
	{
		$themes = $admin->getAvailableThemes();
		$curTheme = $osimo->getCurrentTheme(true);
		$themePreview = $admin->getThemePreview($curTheme);
	?>	
		<div id="theme-wrap" class="user-content-box clearfix">
			<div id="active-theme-wrap">
				<h3>Active theme</h3>
				<div id="active-theme-info">
					<div id="active-theme-preview">
						<img src="<?php echo $themePreview; ?>" width="300px" height="300px" alt="preview" />
					</div>
					<div id="active-theme-text">
						<?php $themeInfo = $admin->getThemeInfo($curTheme); ?>
						<h4><?php 
						if($themeInfo['title']==''){ echo $curTheme; }
						else{ echo $themeInfo['title']; }
						?></h4>
						<p><strong>Author:</strong> <?php echo $themeInfo['author']; ?></p>
						<p><strong>Description:</strong> <?php echo $themeInfo['description']; ?></p>
						<p><strong>Version:</strong> <?php echo $themeInfo['version']; ?></p>
						<p><strong>Release Date:</strong> <?php echo $themeInfo['release']; ?></p>
						<p><strong>Other Info:</strong> <?php echo $themeInfo['other']; ?></p>
					</div>
				</div>
			</div>
			<div id="inactive-theme-wrap">
				<h3>Available Themes</h3>
				<table id="inactive-theme-table">
					<tr>
						<?php
						$rows = ceil((count($themes)-1)/3);
						$num = $rows*3;
						$count=0;
						for($i=0;$i<=$num;$i++)
						{
							if(isset($themes[$i]))
							{
								if($themes[$i]['folderName']!=$curTheme)
								{
									$preview = $admin->getThemePreview($themes[$i]['folderName']);
									?>
									<td class="inactiveTheme" onclick="setActiveTheme('<?php echo $themes[$i]['folderName']; ?>')">
										<div class="theme-preview-image"><img src="<?php echo $preview; ?>" width="200px" height="200px" /></div>
										<div class="theme-preview-info"><p><?php echo $themes[$i]['cleanName']; ?></p></div>
									</td>
									<?
									if(($count+1)%3==0){ echo "</tr><tr>\n"; }
									$count++;
								}
							}
							else
							{
								echo "<td></td>\n";
								if(($count+1)%3==0){ echo "</tr><tr>\n"; }
								$count++;
							}
						}
						?>
					</tr>
				</table>
			</div>
		</div>
	<?
	}
	elseif($page=='editor')
	{
		$themes = $admin->getAvailableThemes();
		$curTheme = $osimo->getCurrentTheme(true);
		?>
		<div id="theme-editor-wrap" class="user-content-box">
			<h3>Theme Editor</h3>
			<form action="#" type="post">
				<p>Edit theme: 
				<select id="themepicker">
				<?php
	 				for($i=0;$i<count($themes);$i++)
				 	{
				 		echo "<option value={$themes[$i]['folderName']}";
				 		if($themes[$i]['folderName']==$curTheme){ echo " selected"; }
				 		echo ">{$themes[$i]['cleanName']}</option>";
				 	}
				?>
				</select>
				<input type="button" value="Edit Theme" onclick="editTheme()" />
			</form>
			<div id="editor-content">
				<div id="editor-left">
					<form action="#" method="post">
						<p id="theme-file-title">Now editing:</p>
						<textarea id="theme-editor" wrap="off" name="theme-editor"></textarea>
						<p><input type="button" value="Update" onclick="updateThemeFile()" /></p>
					</form>
				</div>
				<div id="editor-right">
					<?php
						include(ABS_ADMIN_INCLUDES.'ajax/theme.php');
						outputThemeFileList($curTheme);
					?>
				</div>
			</div>
		</div>
		<?php
	}
	elseif($page=='manageusers')
	{
		
		$totalPages = $osimo->getPagination('table=users','num=10');
		?>
		<h3 style="width: 400px;float: left;color:#444444;">Manage Users</h3><h3 style="width: 460px; float: right;color:#444444;">Controls</h3>
		<div id="userlist-wrap">
			<ul id="userlist">
				<?php
					include(ABS_ADMIN_INCLUDES.'ajax/user.php');
					outputUserList(1,10,'username','ASC');
				?>
			</ul>
		</div>
		<div id="user-controls-wrap">
			<div id="user-page-controls" class="user-content-box">
				<h4>Page <span id="user-curpage">1</span> of <span id="user-totpage"><?php echo $totalPages; ?></span><span id="user-change-page"><a href="javascript:userListPageControl('first')"><img src="img/icons/resultset_first.png" alt="First" title="First" /></a>&nbsp;<a href="javascript:userListPageControl('previous')"><img src="img/icons/resultset_previous.png" alt="Previous" title="Previous" /></a>&nbsp;<a href="javascript:userListPageControl('next')"><img src="img/icons/resultset_next.png" alt="Next" title="Next" /></a>&nbsp;<a href="javascript:userListPageControl('last')"><img src="img/icons/resultset_last.png" alt="Last" title="Last" /></a></span></h4>
				<form action="#" method="post">
					 <p>Jump to page: 
						<select id="jump-to-page" onchange="userListPageControl('jump')">
						    <?php
						    	outputUserPageJumpList($totalPages);
						    ?>
						</select>
					</p>
					<p>Display per page: 
						<select id="num-users-per-page" onchange="userListNumPerPage()">
							<option value="10">10 Users</option>
							<option value="20">20 Users</option>
							<option value="50">50 Users</option>
							<option value="100">100 Users</option>
						</select>
					</p>
					<p>Sort: 
						<select id="sort-item" onchange="userListChangeSort()">
							<option value="username">Username</option>
							<option value="email">Email</option>
							<option value="birthday">Birthday</option>
							<option value="posts"># of Posts</option>
							<option value="time_joined">Date Joined</option>
						</select>
						<select id="sort-order" onchange="userListChangeSort()">
							<option value="ASC">Ascending</option>
							<option value="DESC">Descending</option>
						</select>
					</p>
				</form>
			</div>
			<div id="user-quick-info" class="user-content-box">
				<h4 id="quick-user-info-title">User Quick Info</h4>
			</div>
		</div>
		<script>
			totPage = $('#user-totpage').html();
		</script>
		<?php
	}
	elseif($page=='banlist')
	{
		include_once(ABS_ADMIN_INCLUDES.'ajax/forum.php');
		$forums = $admin->getForumList('all=true');
		echo "<pre>";
		print_r(parseForums($forums));
		echo "</pre>";
	}
	elseif($page=='usersearch')
	{
		?>
		<div id="user-search-wrap">
			<form action="#" method="post">
				<p><input type="text" id="user-search" value="enter username here" onfocus="userSearchOnTxt()" onblur="userSearchOffTxt()" /></p>
			</form>
		</div>
		<div id="user-search-results">
			<h3 id="user-search-begin">osimo user database search<br /><small>type in search box to begin</small></h3>
		</div>
		<script>
			initUserSearch();
		</script>
		<?php
	}
	elseif($page=='siteinfo')
	{
		$info = $admin->getSiteInfo();
		?>
		<form action="#">
			<div class="option-wrap">
				<h4>Site Title</h4>
				<p><input type="text" value="<?php echo $info['site_title']; ?>" style="width: 400px;" id="option-site-title" /></p>
			</div>
			<div class="option-wrap">
				<h4>Site Description</h4>
				<p><input type="text" value="<?php echo $info['site_description']; ?>" style="width: 600px;" id="option-site-desc" /></p>
			</div>
			<div class="option-wrap">
				<h4>Admin Email</h4>
				<p><input type="text" value="<?php echo $info['admin_email']; ?>" style="width: 300px;" id="option-admin-email" /></p>
			</div>
			<div class="option-wrap">
				<h4>Server Time Zone</h4>
				<select id="option-admin-timezone" style="margin: 5px 0 5px 10px;">
      				<option value="-12.0" <?php if($info['server_time_zone']==-12.0){ echo "selected"; } ?>>(GMT -12:00) Eniwetok, Kwajalein</option>
      				<option value="-11.0" <?php if($info['server_time_zone']==-11.0){ echo "selected"; } ?>>(GMT -11:00) Midway Island, Samoa</option>
      				<option value="-10.0" <?php if($info['server_time_zone']==-10.0){ echo "selected"; } ?>>(GMT -10:00) Hawaii</option>
      				<option value="-9.0" <?php if($info['server_time_zone']==-9.0){ echo "selected"; } ?>>(GMT -9:00) Alaska</option>
      				<option value="-8.0" <?php if($info['server_time_zone']==-8.0){ echo "selected"; } ?>>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
      				<option value="-7.0" <?php if($info['server_time_zone']==-7.0){ echo "selected"; } ?>>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
      				<option value="-6.0" <?php if($info['server_time_zone']==-6.0){ echo "selected"; } ?>>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
      				<option value="-5.0" <?php if($info['server_time_zone']==-5.0){ echo "selected"; } ?>>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
      				<option value="-4.0" <?php if($info['server_time_zone']==-4.0){ echo "selected"; } ?>>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
      				<option value="-3.5" <?php if($info['server_time_zone']==-3.5){ echo "selected"; } ?>>(GMT -3:30) Newfoundland</option>
      				<option value="-3.0" <?php if($info['server_time_zone']==-3.0){ echo "selected"; } ?>>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
      				<option value="-2.0" <?php if($info['server_time_zone']==-2.0){ echo "selected"; } ?>>(GMT -2:00) Mid-Atlantic</option>
      				<option value="-1.0" <?php if($info['server_time_zone']==-1.0){ echo "selected"; } ?>>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
      				<option value="0.0" <?php if($info['server_time_zone']==0.0){ echo "selected"; } ?>>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
      				<option value="1.0" <?php if($info['server_time_zone']==1.0){ echo "selected"; } ?>>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
      				<option value="2.0" <?php if($info['server_time_zone']==2.0){ echo "selected"; } ?>>(GMT +2:00) Kaliningrad, South Africa</option>
      				<option value="3.0" <?php if($info['server_time_zone']==3.0){ echo "selected"; } ?>>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
      				<option value="3.5" <?php if($info['server_time_zone']==3.5){ echo "selected"; } ?>>(GMT +3:30) Tehran</option>
      				<option value="4.0" <?php if($info['server_time_zone']==4.0){ echo "selected"; } ?>>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
      				<option value="4.5" <?php if($info['server_time_zone']==4.5){ echo "selected"; } ?>>(GMT +4:30) Kabul</option>
      				<option value="5.0" <?php if($info['server_time_zone']==5.0){ echo "selected"; } ?>>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
      				<option value="5.5" <?php if($info['server_time_zone']==5.5){ echo "selected"; } ?>>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
      				<option value="5.75" <?php if($info['server_time_zone']==5.75){ echo "selected"; } ?>>(GMT +5:45) Kathmandu</option>
      				<option value="6.0" <?php if($info['server_time_zone']==6.0){ echo "selected"; } ?>>(GMT +6:00) Almaty, Dhaka, Colombo</option>
      				<option value="7.0" <?php if($info['server_time_zone']==7.0){ echo "selected"; } ?>>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
      				<option value="8.0" <?php if($info['server_time_zone']==8.0){ echo "selected"; } ?>>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
      				<option value="9.0" <?php if($info['server_time_zone']==9.0){ echo "selected"; } ?>>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
      				<option value="9.5" <?php if($info['server_time_zone']==9.5){ echo "selected"; } ?>>(GMT +9:30) Adelaide, Darwin</option>
      				<option value="10.0" <?php if($info['server_time_zone']==10.0){ echo "selected"; } ?>>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
      				<option value="11.0" <?php if($info['server_time_zone']==11.0){ echo "selected"; } ?>>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
      				<option value="12.0" <?php if($info['server_time_zone']==12.0){ echo "selected"; } ?>>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
				</select>
				<p style="font-size: 12px; margin-left: 5px;">(server time is currently <?php echo date('F j, Y g:ia'); ?>)</p>
			</div>
			<div class="option-wrap">
				<h4>Email Notification <small>(not responsible for inbox flooding)</small></h4>
				<p><input type="checkbox" id="email-newuser" <?php if($info['email_new_user']=='true'){ echo "checked"; } ?>/>New Users</p>
			</div>
			<div class="option-wrap">
				<h4>Registration</h4>
				<p>Status: 
				<select id="option-registration">
					<option value="true" <?php if($info['registration']=='true'){ echo "selected"; } ?>>Open</option>
					<option value="false" <?php if($info['registration']=='false'){ echo "selected"; } ?>>Closed</option>
				</select>
				</p>
			</div>
			<div class="option-wrap">
				<h4>Forum Options</h4>
				<p># of Threads Per Forum Page: <input type="text" value="<?php echo $info['thread_num_per_page']; ?>" id="option-num-threads" /></p>
				<p># of Posts per Thread Page: <input type="text" value="<?php echo $info['post_num_per_page']; ?>" id="option-num-posts" /></p>
			</div>
			<input type="button" value="Save Options" onclick="updateGeneralOptions()" id="options-submit" />
		</form>
		<?
	}
	elseif($page=='smilies')
	{
		$activeName = $admin->getActiveSmileyName();
		?>
		<div id="smiley-customize" class="user-content-box clearfix">
			<h3>Customize Active Smiley Code (<?php echo $activeName; ?>)</h3>
			<form id="edit-smilies">
				<table id="active-smiley-table">
					<tr>
					<?php 
						$activeSmilies = $admin->getActiveSmilies();
						$rows = ceil((count($activeSmilies)-1)/4);
					    $num = $rows*4;
					    $count=0;
					    for($i=0;$i<=$num;$i++)
					    {
					        if(isset($activeSmilies[$i]))
					        {
					        	?>
					        	<td class="activeSmiley">
					        	    <p><?php echo $activeSmilies[$i]['imgURL']; ?>
					        	    <input name="<?php echo $activeSmilies[$i]['fileName']; ?>" class="active-smiley" type="text" value="<?php echo $activeSmilies[$i]['code']; ?>" /></p>
					        	</td>
					        	<?
					        	if(($count+1)%4==0){ echo "</tr><tr>\n"; }
					        	$count++;
				
					        }
					        else
					        {
					        	echo "<td></td>\n";
					        	if(($count+1)%4==0){ echo "</tr><tr>\n"; }
					        	$count++;
					        }
					    }
					?>
					</tr>
				</table>
				
				<input type="button" value="Save" class="smiley-save" onclick="editActiveSmilies()" />
			</form>
		</div>
		
		<div id="smiley-chooser" class="user-content-box clearfix">
			<h3>Available Smiley Sets</h3>
			<table id="inactive-smiley-table">
			    <tr>
			    	<?php
			    	$smilies = $admin->getAvailableSmilies();
			    	$rows = ceil((count($smilies)-1)/6);
			    	$num = $rows*6;
			    	$count=0;
			    	for($i=0;$i<=$num;$i++)
			    	{
			    		if(isset($smilies[$i]))
			    		{
			    			if($smilies[$i]['folderName']!=$curSmilies)
			    			{
			    				$preview = $admin->getSmileyPreview($smilies[$i]['folderName']);
			    				?>
			    				<td class="inactiveSmiley" onclick="setActiveSmilies('<?php echo $smilies[$i]['folderName']; ?>')">
			    					<div class="smiley-preview-images"><?php echo $preview; ?></div>
			    					<div class="smiley-preview-info"><p><?php echo $smilies[$i]['cleanName']; ?></p></div>
			    				</td>
			    				<?
			    				if(($count+1)%6==0){ echo "</tr><tr>\n"; }
			    				$count++;
			    			}
			    		}
			    		else
			    		{
			    			echo "<td></td>\n";
			    			if(($count+1)%6==0){ echo "</tr><tr>\n"; }
			    			$count++;
			    		}
			    	}
			    	?>
			    </tr>
			</table>
		</div>
		<?php
	}
}
?>