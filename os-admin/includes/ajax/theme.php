<?php
session_start();
if(!isset($_SESSION['admin'])){ exit; }
include_once('../../../os-includes/dbconnect.php');
include_once('../../../os-includes/paths.php');
include_once('../../../os-includes/osimo.php');
include_once('../admin.php');
$osimo = new Osimo();
$admin = new OsimoAdmin();

if(isset($_POST['setTheme'])){ setActiveTheme($_POST['setTheme']); }
if(isset($_POST['setSmilies'])){ setActiveSmilies($_POST['setSmilies']); }
if(isset($_POST['editTheme'])){ outputThemeFileList($_POST['theme']); }
if(isset($_POST['editThemeFile'])){ loadThemeFile($_POST['theme'],$_POST['file']); }
if(isset($_POST['updateThemeFile'])){ updateThemeFile($_POST['theme'],$_POST['file'],$_POST['content']); }
if(isset($_POST['editActiveSmilies'])){ editActiveSmilies($_POST['smiley'],$_POST['smileyCode']); }

function setActiveTheme($theme)
{
	$theme = secureContent($theme);
	
	$query = "UPDATE config SET value='$theme' WHERE name='current_theme' LIMIT 1";
	$result = mysql_query($query);
	if($result){ echo "1"; }
	else{ echo "0"; }
}

function setActiveSmilies($smilies)
{
	$smiles = secureContent($smilies);
	
	/* First we need to check if the smilies are in the DB yet */
	$query1 = "SELECT COUNT(*) FROM smilies WHERE smileySet='$smilies'";
	$result1 = mysql_query($query1);
	if(reset(mysql_fetch_row($result1))==0)
	{
		$insert = "INSERT INTO smilies (id,smileySet,code,image) VALUES ";
		/* Add smilies to database */
		$smileyfolder = ABS_THEME_FOLDER.'../smilies/'.$smilies.'/';
		$dh = opendir($smileyfolder);
		$count=0;
		while (false !== ($smiley = readdir($dh))) {
    	    if(is_file($smileyfolder.$smiley)&&$smiley!='.'&&$smiley!='..')
    	    {
    	    	if($count!=0){ $insert .= ","; }
    	    	$insert .= "(NULL,'$smilies',':unset:','$smiley')";
    	    	$count++;
    	    }
		}
		closedir($dh);
    }
    $insertResult = mysql_query($insert);
    
    $query2 = "UPDATE config SET value='$smilies' WHERE name='current-smilies' LIMIT 1";
    $result2 = mysql_query($query2);
    
    if($result2){ echo "1"; }
    else{ echo "0"; }
}

function editActiveSmilies($name,$code)
{	
	$query1 = "SELECT value FROM config WHERE name='current-smilies' LIMIT 1";
	$result1 = mysql_query($query1);
	if($result1){ $smileySet = reset(mysql_fetch_row($result1)); }
	for($i=0;$i<count($name);$i++)
	{
		$query = "UPDATE smilies SET code='{$code[$i]}' WHERE image='{$name[$i]}' AND smileySet='$smileySet' LIMIT 1";
		$result = mysql_query($query);
	}
	
	if($result){ echo "1"; }
	else{ echo "0"; }
}

function outputThemeFileList($name)
{
	global $osimo,$admin;
	$files = $admin->getThemeFilelist($name);
	if(is_array($files))
	{
		echo "<ul id=\"theme-filelist\">\n";
		foreach($files as $file)
		{
			echo "<li><a href=\"javascript:editThemeFile('$name','$file')\">$file</a></li>\n";
		}
		echo "</ul>\n";
	}
}

function loadThemeFile($theme,$file)
{
	header('Content-Type: text/plain');
	if(strpos($theme,"..")||strpos($file,"..")){ return false; }
	$path = ABS_THEME_FOLDER.$theme."/".$file;
	
	$handle = fopen($path,"r");
	echo fread($handle,filesize($path));
	fclose($handle);
}

function updateThemeFile($theme,$file,$content)
{
	if(strpos($theme,"..")||strpos($file,"..")){ return false; }
	$path = ABS_THEME_FOLDER.$theme."/".$file;
	
	$handle = fopen($path,"w");
	$write = fwrite($handle,stripslashes($content));
	fclose($handle);
	if($write){ echo "1"; }
	else{ echo "0"; }
}
?>