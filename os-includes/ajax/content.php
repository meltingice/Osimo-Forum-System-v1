<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/ajax/content.php - ajax backend for outputting preset html via ajax
*/

session_start();
include_once('../dbconnect.php'); //connects to database
include_once('../paths.php');
include_once('../osimo.php');
$osimo = new Osimo(); //makes magic happen

if(isset($_POST['content'])&&$_POST['content']=='bbhelp'){ getBBHelpBox(); }
if(isset($_POST['content'])&&$_POST['content']=='forgotpassword'){ 
	if($_POST['step']==0){ getForgotPasswordBox(0); }
	elseif($_POST['step']==1&&$_POST['type']==-1){ getForgotPasswordBox(1); }
	elseif($_POST['step']==1&&$_POST['type']==1){
		include_once(ABS_INCLUDES.'ajax/user.php');
		resetPassword(1,1,$_POST['user']);
	}
	elseif($_POST['step']==1&&$_POST['type']==2){
		include_once(ABS_INCLUDES.'ajax/user.php');
		resetPassword(1,2,$_POST['email']);
	}
	elseif($_POST['step']==2)
	{
		include_once(ABS_INCLUDES.'ajax/user.php');
		$data['code'] = $_POST['code'];
		$data['pass1'] = $_POST['pass1'];
		$data['pass2'] = $_POST['pass2'];
		resetPassword(2,1,$data);
	}
}
if(isset($_POST['content'])&&$_POST['content']=='editpostbox'){ getPostEditBox($_POST['postID']); }
if(isset($_POST['content'])&&$_POST['content']=='usercpbox'){ getUserCP(); }
if(isset($_POST['content'])&&$_POST['content']=='editprofilebox'){ getProfileEditBox($_POST['userID']); }
if(isset($_POST['updatePagination'])){ updatePagination($_POST['page'],$_POST['id'],$_POST['activePage']); }

function getBBHelpBox()
{
	global $osimo;
	?>
	<div id="osimo-bbcode-help">
		<h4>Basic BBCodes</h4>
		<table class="osimo-bbcode-table" cellpadding="2px" cellspacing="2px">
			<tr>
				<td style="background-color: #c4c4c4; color: #f8f8f8;"><strong>Code</strong></td>
				<td style="background-color: #c4c4c4; color: #f8f8f8;"><strong>Output</strong></td>
			</tr>
			<tr>
				<td>[b]Bold Text[/b]</td>
				<td><strong>Bold Text</strong></td>
			</tr>
			<tr>
				<td>[i]Italic Text[/i]</td>
				<td><i>Italic Text</i></td>
			</tr>
			<tr>
				<td>Underlined Text</td>
				<td><u>Underlined Text</u></td>
			</tr>
			<tr>
				<td>
					[list]<br />
					[*]Item 1<br />
					[*]Item 2<br />
					[/list]
				</td>
				<td>
					<ul>
						<li>Item 1</li>
						<li>Item 2</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td>[url]http://meltingice.net[/url]</td>
				<td><a href="http://meltingice.net">http://meltingice.net</a></td>
			</tr>
			<tr>
				<td>[email]someone@something.com[/email]</td>
				<td><a href="mailto:someone@something.com">someone@something.com</a></td>
			</tr>
			<tr>
				<td>[img]http://getosimo.com/forums/os-content/avatars/1.png[/img]</td>
				<td><img src="http://getosimo.com/forums/os-content/avatars/1.png" alt="image" /></td>
			</tr>
			<tr>
				<td>[quote]Quoted text[/quote]</td>
				<td><blockquote><span class="bbhelp-blockquote-title">Quote:</span><br />Quoted text</blockquote></td>
			</tr>
			<tr>
				<td>[code]$var = 5;[/code]</td>
				<td><code>$var = 5;</code></td>
			</tr>
			<tr>
				<td>[right]Right-Aligned Text[/right]</td>
				<td><div style="text-align: right;">Right-Aligned Text</div></td>
			</tr>
			<tr>
				<td>[left]Left-Aligned Text[/left]</td>
				<td><div style="text-align: left;">Left-Aligned Text</div></td>
			</tr>
			<tr>
				<td>[center]Center-Aligned Text[/center]</td>
				<td><div style="text-align: center">Center-Aligned Text</div></td>
			</tr>
			<tr>
				<td>[spoiler]Many secrets here![/spoiler]</td>
				<td><span style="background: #000000 none repeat scroll 0% 0%; color: #000000; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial">Many secrets here!</span></td>
			</tr>
			<tr>
				<td>[nocode]Ignores [b]BBCode[/b][/nocode]</td>
				<td>Ignores [b]BBCode[/b]</td>
			</tr>
		</table>
		<h4>Fancy BBCode</h4>
		<table class="osimo-bbcode-table" cellpadding="2px" cellspacing="2px">
			<tr>
				<td style="background-color: #c4c4c4; color: #f8f8f8;"><strong>Code</strong></td>
				<td style="background-color: #c4c4c4; color: #f8f8f8;"><strong>Output</strong></td>
			</tr>
			<tr>
				<td>[email=someone@something.com]Email Me[/email]</td>
				<td><a href="mailto:someone@something.com">Email Me</a></td>
			</tr>
			<tr>
				<td>[url=http://meltingice.net]My Website[/url]</td>
				<td><a href="http://meltingice.net">My Website</a></td>
			</tr>
			<tr>
				<td>[size=24]Big Text[/size]</td>
				<td><span style="font-size: 24px;">Big Text</span></td>
			</tr>
			<tr>
				<td>[font=Georgia]Fancy Text[/font]</td>
				<td><span style="font-family: Georgia">Fancy Text</span></td>
			</tr>
			<tr>
				<td>[color=red]Red Text[/color]<br />[color=#478dbe]Blue-ish Text[/color]</td>
				<td><span style="color:red;">Red Text</span><br /><span style="color: #478dbe">Blue-ish Text</span></td>
			</tr>
			<tr>
				<td>[quote=meltingice]Quoted Text[/quote]</td>
				<td><blockquote><span class="bbhelp-blockquote-title">Quote: original post by meltingice</span><br />Quoted Text</blockquote></td>
			</tr>
			<tr>
				<td>[align=right]Right-Aligned Text[/align]<br />
				[align=center]Center-Aligned Text[/align]<br />
				[align=left]Left-Aligned Text[/align]</td>
				<td><div style="text-align:right">Right-Aligned Text</div>
				<div style="text-align: center">Center-Aligned Text</div>
				<div style="text-align: left">Left-Aligned Text</div></td>
			</tr>
		</table>
		
		<h4>Smiley Code</h4>
		<table class="osimo-bbcode-table" cellpadding="2px" cellspacing="2px">
			<tr>
				<td style="background-color: #c4c4c4; color: #f8f8f8;">Code</td>
				<td style="background-color: #c4c4c4; color: #f8f8f8;">Smiley</td>
			</tr>
			<?php
				$activeSmilies = $osimo->getActiveSmilies();
				
				for($i=0;$i<count($activeSmilies);$i++)
				{
					echo "<tr>\n";
						echo "<td>{$activeSmilies[$i]['code']}</td>";
						echo "<td>{$activeSmilies[$i]['imgURL']}</td>";
					echo "</tr>\n";
				}
			?>
		</table>
	</div>
	<?php
}

function getForgotPasswordBox($step)
{
	if($step==0):
	?>
	<div id="osimo_forgotpassword">
		<h3>Step 1</h3>
		<p>There are two ways you can reset your password.  You can either enter your username below on the left, or you can enter your email address below on the right.</p>
		<div id="osimo_forgotpassword_left">
			<h4>Reset by Username</h4>
			<p class="osimo-input-label">Username</p>
			<input class="osimo-input-field" type="text" id="osimo_forgotpass_username" />
			<input type="button" value="Submit" onclick="resetPassword(1,1)" />
		</div>
		<div id="osimo_forgotpassword_right">
			<h4>Reset by Email</h4>
			<p class="osimo-input-label">Email Address</p>
			<input class="osimo-input-field" type="text" id="osimo_forgotpass_email" />
			<input type="button" value="Submit" onclick="resetPassword(1,2)" />
		</div>
		<input class="osimo-submit-button" type="button" value="Skip to Step 2" onclick="resetPassword(1,-1)" />
	</div>
	<?php
	elseif($step==1):
	?>
	<div id="osimo_forgotpassword">
		<h3>Step 2</h3>
		<p>Once you have received an email with a reset code, copy and paste it here and type in a new password for you to use.</p>
		<div class="osimo_forgotpassword_wrap">
			<p class="osimo-input-label">Reset Code</p>
			<input type="text" id="osimo_forgotpass_code" class="osimo-input-field" />
			<p class="osimo-input-label">New Password</p>
			<input type="password" id="osimo_forgotpass_newpass1" class="osimo-input-field" />
			<p class="osimo-input-label">New Password Again</p>
			<input type="password" id="osimo_forgotpass_newpass2" class="osimo-input-field" />
		</div>
		<input class="osimo-submit-button" type="button" value="Submit" onclick="resetPassword(2,1)" />
	</div>
	<?php
	elseif($step==2):
	?>
	<div id="osimo_forgotpassword">
		<h3>Finished!</h3>
		<p>Your password has successfully been changed, you may now log in with your new password.</p>
		<input class="osimo-submit-button" type="button" value="Close" onclick="$('#osimo_forgotpasswordbox').dialog('destroy');" />
	</div>
	<?php
	endif;
}

function getPostEditBox($postID)
{
	global $osimo;
	if(($osimo->userIsPostOwner($postID)&&$osimo->userCanEditPosts())||$osimo->userIsModerator()||$osimo->userIsAdmin())
	{
		include('post.php');
		
		$content = getPostContent($postID);
		$threadID = $osimo->getPostThread($postID);
		
		?>
		<form action="javascript:editPost(<?php echo $postID.",".$threadID; ?>)">
			<textarea id="osimo_editpostcontent"><?php echo $content; ?></textarea>
			<input type="button" value="Submit" onclick="editPost(<?php echo $postID.",".$threadID; ?>)" />
			<input type="button" value="Cancel" onclick="$('#osimo_editpostbox').dialog('close');" />
		</form>
		<?php
	}
	else
	{
		echo "0";
	}
}

function getProfileEditBox($userID)
{
	global $osimo;
	$user = $osimo->getLoggedInUser();
	
	if($userID!=$user['ID']){ exit; }
	
	$user_info = $osimo->getUserProfileInfo($userID);
	
	$birthday_month = adodb_date('F',$user_info['birthday']);
	$birthday_day = adodb_date('j',$user_info['birthday']);
	$birthday_year = adodb_date('Y',$user_info['birthday']);
	
	?>
	<div class="osimo_profile-content" style="float: none;text-align: left; margin: auto;padding-bottom:20px;">
		<h4><small>User Information</small></h4>
		<form action="javascript:updateProfile('info')" id="osimo_profile_info">
			<table>
				<tr>
					<td><strong>Sex:</strong></td>
					<td><select name="field_sex">
						<option <?php if($user_info['field_sex']=="Male"){ echo "selected"; } ?>>Male</option>
						<option <?php if($user_info['field_sex']=="Female"){ echo "selected"; } ?>>Female</option>
					</select></td>
				</tr>
				<tr>
					<td><strong>Birthday:</strong></td>
					<td>
						<select name="birthday_month">
							<option value="1"<?php if($birthday_month=="January"){ echo "selected"; } ?>>January</option>
							<option value="2"<?php if($birthday_month=="Feburary"){ echo "selected"; } ?>>Feburary</option>
							<option value="3"<?php if($birthday_month=="March"){ echo "selected"; } ?>>March</option>
							<option value="4"<?php if($birthday_month=="April"){ echo "selected"; } ?>>April</option>
							<option value="5"<?php if($birthday_month=="May"){ echo "selected"; } ?>>May</option>
							<option value="6"<?php if($birthday_month=="June"){ echo "selected"; } ?>>June</option>
							<option value="7"<?php if($birthday_month=="July"){ echo "selected"; } ?>>July</option>
							<option value="8"<?php if($birthday_month=="August"){ echo "selected"; } ?>>August</option>
							<option value="9"<?php if($birthday_month=="September"){ echo "selected"; } ?>>September</option>
							<option value="10"<?php if($birthday_month=="October"){ echo "selected"; } ?>>October</option>
							<option value="11"<?php if($birthday_month=="November"){ echo "selected"; } ?>>November</option>
							<option value="12"<?php if($birthday_month=="December"){ echo "selected"; } ?>>December</option>
						</select>
						<select name="birthday_day">
							<?php
							for($i=1;$i<=31;$i++)
							{
								echo "<option";
								if($birthday_day==$i){ echo " selected"; }
								echo ">$i</option>";
							}
							?>
						</select>
						<select name="birthday_year">
							<?php
							for($i=1920;$i<adodb_date('Y');$i++)
							{
								echo "<option";
								if($birthday_year==$i){ echo " selected"; }
								echo ">$i</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><strong>Website:</strong></td>
					<td><input type="text" name="field_website" value="<?php echo $user_info['field_website_unformatted']; ?>" /></td>
				</tr>
				<tr>
					<td><strong>About:</strong></td>
					<td><input type="text" name="field_about" value="<?php echo $user_info['field_about']; ?>" /></td>
				</tr>
				<tr>
					<td><strong>Interests:</strong></td>
					<td><input type="text" name="field_interests" value="<?php echo $user_info['field_interests']; ?>" /></td>
				</tr>
			</table>
			
			<input type="button" value="Update" onclick="updateProfile('info')" style="width: 100px;margin: auto; display: block;"/>
		</form>
		
		<h4><small>Contact Information</small></h4>
		<form action="javascript:updateProfile('contact')" id="osimo_profile_contact">
			<table>
				<tr>
					<td><strong>AIM:</strong></td>
					<td><input type="text" name="field_aim" value="<?php echo $user_info['field_aim']; ?>" /></td>
				</tr>
				<tr>
					<td><strong>Jabber:</strong></td>
					<td><input type="text" name="field_jabber" value="<?php echo $user_info['field_jabber']; ?>" /></td>
				</tr>
				<tr>
					<td><strong>MSN:</strong></td>
					<td><input type="text" name="field_msn" value="<?php echo $user_info['field_msn']; ?>" /></td>
				</tr>
				<tr>
					<td><strong>Yahoo IM:</strong></td>
					<td><input type="text" name="field_yim" value="<?php echo $user_info['field_yim']; ?>" /></td>
				</tr>
				<tr>
					<td><strong>ICQ:</strong></td>
					<td><input type="text" name="field_icq" value="<?php echo $user_info['field_icq']; ?>" /></td>
				</tr>
			</table>

			<input type="button" value="Update" onclick="updateProfile('contact')" style="width: 100px;margin: auto; display: block;"/>
		</form>

		<h4><small>Biography</small></h4>
		<form action="javascript:updateProfile('bio')" id="osimo_profile_bio_container">
			<div id="osimo_profile_bio" style="width: 478px !important; margin: 0 0 20px 10px;float:none;">
				<textarea name="field_biography"><?php echo $user_info['field_biography_unformatted']; ?></textarea>
			</div>
			<input type="button" value="Update" onclick="updateProfile('bio')" style="width: 100px;margin: auto; display: block;"/>
		</form>
		
		<h4><small>Signature</small></h4>
		<form action="javascript:updateProfile('sig')" id="osimo_profile_sig_container">
			<div id="osimo_profile_sig" style="width: 478px; margin: 0 0 20px 10px;float:none;">
				<p>Preview:</p>
				<div id="osimo_profile_sig_preview"><?php echo $osimo->getUserSignature($userID,true,true); ?></div>
				<p>Edit Signature:</p>
				<textarea name="signature"><?php echo $osimo->getUserSignature($userID,false,true); ?></textarea>
			</div>
			<input type="button" value="Update" onclick="updateProfile('sig')" style="width:100px; margin:auto; display: block;"/>
		</form>
		
		<h4><small>Avatar</small></h4>
		<form enctype="multipart/form-data" action="os-includes/upload.php" id="osimo_profile_ava_container" method="post">
			<div id="osimo_profile_ava" style="width: 478px; margin: 0 0 20px 10px;float:none;">
				<div id="osimo_profile_ava_image">
					<?php echo $osimo->getUserAvatar($userID,'../../'); ?>
				</div>
				<div id="osimo_profile_ava_upload">
					<p>Max width: 120px | Max height: 120px</p>
					<p><input type="file" name="avatar_upload" /></p>
					<p><input type="submit" value="Upload" style="width: 100px;" /></p>
				</div>
			</div>
		</form>
	</div>
	<?php
}

function getUserCP()
{
	global $osimo;
	
	$user = $osimo->getLoggedInUser();
	if(!$user){ echo "mustlogin"; exit; }
	?>
	<div class="osimo_usercp_dropdown" onclick="showUserCPSection('personal')">
		<img src="os-includes/img/add.png" alt="expand" /><p>Personal Information</p>
	</div>
	<div id="osimo_usercp_personal" style="display:none">
		<form action="javascript:updatePersonalInfo()">
			<p class="osimo-input-label">display name</p>
			<p class="osimo-usercp-input-field"><input type="text" value="<?php echo $user['display_name']; ?>" id="osimo_usercp_displayname" /></p>
			<p class="osimo-input-label">email address</p>
			<p class="osimo-usercp-input-field"><input type="text" value="<?php echo $user['email']; ?>" id="osimo_usercp_emailaddr" /></p>
			<p class="osimo-input-label">time zone</p>
			<select id="osimo_usercp_timezone">
      			<option value="-12.0" <?php if($user['time_zone']==-12.0){ echo "selected"; } ?>>(GMT -12:00) Eniwetok, Kwajalein</option>
      			<option value="-11.0" <?php if($user['time_zone']==-11.0){ echo "selected"; } ?>>(GMT -11:00) Midway Island, Samoa</option>
      			<option value="-10.0" <?php if($user['time_zone']==-10.0){ echo "selected"; } ?>>(GMT -10:00) Hawaii</option>
      			<option value="-9.0" <?php if($user['time_zone']==-9.0){ echo "selected"; } ?>>(GMT -9:00) Alaska</option>
      			<option value="-8.0" <?php if($user['time_zone']==-8.0){ echo "selected"; } ?>>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
      			<option value="-7.0" <?php if($user['time_zone']==-7.0){ echo "selected"; } ?>>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
      			<option value="-6.0" <?php if($user['time_zone']==-6.0){ echo "selected"; } ?>>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
      			<option value="-5.0" <?php if($user['time_zone']==-5.0){ echo "selected"; } ?>>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
      			<option value="-4.0" <?php if($user['time_zone']==-4.0){ echo "selected"; } ?>>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
      			<option value="-3.5" <?php if($user['time_zone']==-3.5){ echo "selected"; } ?>>(GMT -3:30) Newfoundland</option>
      			<option value="-3.0" <?php if($user['time_zone']==-3.0){ echo "selected"; } ?>>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
      			<option value="-2.0" <?php if($user['time_zone']==-2.0){ echo "selected"; } ?>>(GMT -2:00) Mid-Atlantic</option>
      			<option value="-1.0" <?php if($user['time_zone']==-1.0){ echo "selected"; } ?>>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
      			<option value="0.0" <?php if($user['time_zone']==0.0){ echo "selected"; } ?>>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
      			<option value="1.0" <?php if($user['time_zone']==1.0){ echo "selected"; } ?>>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
      			<option value="2.0" <?php if($user['time_zone']==2.0){ echo "selected"; } ?>>(GMT +2:00) Kaliningrad, South Africa</option>
      			<option value="3.0" <?php if($user['time_zone']==3.0){ echo "selected"; } ?>>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
      			<option value="3.5" <?php if($user['time_zone']==3.5){ echo "selected"; } ?>>(GMT +3:30) Tehran</option>
      			<option value="4.0" <?php if($user['time_zone']==4.0){ echo "selected"; } ?>>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
      			<option value="4.5" <?php if($user['time_zone']==4.5){ echo "selected"; } ?>>(GMT +4:30) Kabul</option>
      			<option value="5.0" <?php if($user['time_zone']==5.0){ echo "selected"; } ?>>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
      			<option value="5.5" <?php if($user['time_zone']==5.5){ echo "selected"; } ?>>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
      			<option value="5.75" <?php if($user['time_zone']==5.75){ echo "selected"; } ?>>(GMT +5:45) Kathmandu</option>
      			<option value="6.0" <?php if($user['time_zone']==6.0){ echo "selected"; } ?>>(GMT +6:00) Almaty, Dhaka, Colombo</option>
      			<option value="7.0" <?php if($user['time_zone']==7.0){ echo "selected"; } ?>>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
      			<option value="8.0" <?php if($user['time_zone']==8.0){ echo "selected"; } ?>>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
      			<option value="9.0" <?php if($user['time_zone']==9.0){ echo "selected"; } ?>>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
      			<option value="9.5" <?php if($user['time_zone']==9.5){ echo "selected"; } ?>>(GMT +9:30) Adelaide, Darwin</option>
      			<option value="10.0" <?php if($user['time_zone']==10.0){ echo "selected"; } ?>>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
      			<option value="11.0" <?php if($user['time_zone']==11.0){ echo "selected"; } ?>>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
      			<option value="12.0" <?php if($user['time_zone']==12.0){ echo "selected"; } ?>>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
			</select>
			<h4>password change</h4>
			<p class="osimo-input-label">current password</p>
			<p class="osimo-usercp-input-field"><input type="password" id="osimo_usercp_curpassword" /></p>
			<p class="osimo-input-label">new password</p>
			<p class="osimo-usercp-input-field"><input type="password" id="osimo_usercp_newpassword" /></p>
			<p class="osimo-input-label">new password again</p>
			<p class="osimo-usercp-input-field"><input type="password" id="osimo_usercp_newpassword2" /></p>
			
			<input type="button" value="save changes" onclick="updatePersonalInfo()" class="osimo-usercp-submit" />
		</form>
	</div>
	<div class="osimo_usercp_dropdown" onclick="showUserCPSection('subscriptions')">
		<img src="os-includes/img/add.png" alt="expand" /><p>Forum Subscriptions</p>
	</div>
	<?
}

function updatePagination($page,$id,$activePage=1)
{
	global $osimo;
	
	echo $osimo->getPresetPagination($page,$id,$activePage);
}
?>