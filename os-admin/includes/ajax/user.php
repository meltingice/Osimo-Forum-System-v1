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

if(isset($_POST['loadUserList'])){ outputUserList($_POST['page'],$_POST['numUsers'],$_POST['sort'],$_POST['sortOrder']); }
if(isset($_POST['updatePagination'])){ updatePagination($_POST['num']); }
if(isset($_POST['updateUserPageJumpList'])){ outputUserPageJumpList($_POST['num']); }
if(isset($_POST['quickUserInfo'])){ outputQuickUserInfo($_POST['userID']); }

function outputUserList($page,$num,$sort,$order)
{
	global $osimo,$admin;
	$users = $admin->getUserList($page,$num,$sort,$order);
	
	foreach($users as $user)
	{
	    $avatar = $osimo->getUserAvatar($user['id'],false);
	    echo "<li id=\"user_{$user['id']}\" class=\"user\" onclick=\"loadQuickUserInfo({$user['id']})\">
	    <img src=\"$avatar\" width=\"24\" height=\"24\" />&nbsp;{$user['username']}
	    </li>";
	}
}

function outputUserPageJumpList($totalPages)
{
	for($i=1;$i<=$totalPages;$i++)
	{
		echo "<option value=\"$i\">$i</option>";
	}
}

function updatePagination($num)
{
	global $osimo;
	echo $osimo->getPagination('table=users',"num=$num");
}

function outputQuickUserInfo($userID)
{
	global $osimo;
	$userID = secureContent($userID);

	$query = "SELECT username,email,ip_address,birthday,posts,is_confirmed,is_admin,is_global_mod,time_joined,time_last_visit FROM users WHERE id='$userID' LIMIT 1";
	$result = mysql_query($query);
	
	if($result)
	{
		$user = mysql_fetch_array($result, MYSQL_ASSOC);
		?>
		<h4 id="quick-user-info-title">User Quick Info</h4>
		<?php $ava = $osimo->getUserAvatar($userID,false); ?>
		<div id="quick-ava"><img src="<?php echo $ava; ?>" alt="avatar" /></div>
		<div id="quick-info">
		    <h4><?php 
		    	echo $user['username'];
		    	if($user['is_admin']){ echo " (Admin)"; }
		    	elseif($user['is_global_mod']){ echo "(Moderator)"; }
		    ?></h4>
		    <p>Email: <?php echo "<a href=\"mailto:{$user['email']}\">{$user['email']}</a>"; ?></p>
		    <p>IP Address: <?php echo $user['ip_address']; ?></p>
		    <p>Posts: <?php echo $user['posts']; ?></p>
		    <p>Birthday: <?php echo date('n/j/Y g:ia',$user['birthday']); ?></p>
		    <p>Joined: <?php echo date('n/j/Y g:ia',$user['time_joined']); ?></p>
		    <p>Last Visit: <?php echo date('n/j/Y g:ia',$user['time_last_visit']); ?></p>
		</div>
		<?
	}
	
}
?>