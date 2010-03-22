<?php
session_start();
include_once('dbconnect.php'); //connects to database
include_once('paths.php');
include_once('osimo.php');
$osimo = new Osimo(); //makes magic happen

/* Handles avatar uploads */
if(is_uploaded_file($_FILES['avatar_upload']['tmp_name']))
{
	$user = $osimo->getLoggedInUser();
	if($user==false){ exit; }
	$ext = strtolower(pathinfo($_FILES['avatar_upload']['name'],PATHINFO_EXTENSION));
	
	if($ext!='png'&&$ext!='jpg'&&$ext!='gif'){ header("Location: ../profile.php?id={$user['ID']}"); exit; }
	
	if(move_uploaded_file($_FILES['avatar_upload']['tmp_name'], ABS_AVATAR_FOLDER.$user['ID'].".".$ext))
	{
		/* Resize the avatar to 120px by 120px max */
		resizeImage(ABS_AVATAR_FOLDER.$user['ID'].".".$ext,ABS_AVATAR_FOLDER.$user['ID'].".".$ext,120,120);
		
		/* Delete avatars of different file extentions for the user */
		if($ext!='jpg'){ @unlink(ABS_AVATAR_FOLDER.$user['ID'].".jpg"); }
		if($ext!='png'){ @unlink(ABS_AVATAR_FOLDER.$user['ID'].".png"); }
		if($ext!='gif'){ @unlink(ABS_AVATAR_FOLDER.$user['ID'].".gif"); }
		
		$osimo->writeToSysLog('user-profile-avatar',$user['ID'],$user['name']." uploaded a new avatar");
		
		header("Location: ../profile.php?id={$user['ID']}");
	}
}

/* Resizes an image - $name = input, $filename = output */
function resizeImage($name, $filename, $new_w, $new_h)
{	
	$ext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
	if($ext=='jpg')
	{
		$src_img = @imagecreatefromjpeg($name);
	}
	else if($ext=='png')
	{
		$src_img = @imagecreatefrompng($name);
	}
	else if($ext=='gif')
	{
		$src_img = @imagecreatefromgif($name);
	}
	else
	{
		return false;
	}
	
	$old_x = @imageSX($src_img);
	$old_y = @imageSY($src_img);
	if ($old_x > $old_y)
	{
		$thumb_w = $new_w;
		$thumb_h = $old_y * ($new_h / $old_x);
	}
	else if($old_x < $old_y)
	{
		$thumb_w = $old_x * ($new_w / $old_y);
		$thumb_h = $new_h;
	}
	else if ($old_x == $old_y)
	{
		$thumb_w = $new_w;
		$thumb_h = $new_h;
	}
	
	$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
	@imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
	
	if($ext=='jpg'||$ext=='jpeg')
	{
		@imagejpeg($dst_img, $filename);
	}
	else if($ext=='png')
	{
		@imagepng($dst_img, $filename);
	}
	else if($ext=='gif')
	{
		@imagegif($dst_img, $filename);
	}
	
	imagedestroy($dst_img);
	@imagedestroy($src_img);
}
?>