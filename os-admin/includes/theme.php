<?php
if(!isset($_SESSION['admin'])){ exit; }

function admin_getAvailableThemes()
{
	$themefolder = ABS_THEME_FOLDER;
	
	$dh = opendir($themefolder);
	while (false !== ($folder = readdir($dh))) {
        if(is_dir($themefolder.$folder)&&$folder!='.'&&$folder!='..')
        {
        	$data[] = $folder;
        }
    }
    
    /* Lets make pretty versions of the theme names */
    for($i=0;$i<count($data);$i++)
    {
    	$cleanName = ucwords(str_replace("-"," ",$data[$i]));
    	$themes[$i]['folderName'] = $data[$i];
    	$themes[$i]['cleanName'] = $cleanName;
    }
    
    return $themes;
}

function admin_getAvailableSmilies()
{
	$smileyfolder = ABS_THEME_FOLDER.'../smilies/';
	
	$dh = opendir($smileyfolder);
	while (false !== ($folder = readdir($dh))) {
        if(is_dir($smileyfolder.$folder)&&$folder!='.'&&$folder!='..')
        {
        	$data[] = $folder;
        }
    }
    
    /* Lets make pretty versions of the smiley names */
    for($i=0;$i<count($data);$i++)
    {
    	$cleanName = ucwords(str_replace("-"," ",$data[$i]));
    	$smilies[$i]['folderName'] = $data[$i];
    	$smilies[$i]['cleanName'] = $cleanName;
    }
    
    closedir($dh);
    
    return $smilies;
}

function admin_getThemeFilelist($name)
{
	$name = secureContent($name);
	if(strpos($name,"..")!=false){ return false; }
	
	$themepath = ABS_THEME_FOLDER.$name;
	if(!is_dir($themepath)){ return false; }
	
	if($dh = opendir($themepath))
	{
    	while (false !== ($file = readdir($dh)))
    	{
    		if($file!='.'&&$file!='..'&&is_file(ABS_THEME_FOLDER.$name."/".$file))
    		{
    			$files[] = $file;
    		}
    	}
        
		closedir($dh);
    }
    
    return $files;
}

function admin_getThemePreview($theme)
{
	if(file_exists(ABS_THEME_FOLDER."$theme/preview.jpg")){ return OSIMOPATH."os-content/themes/$theme/preview.jpg"; }
	elseif(file_exists(ABS_THEME_FOLDER."$theme/preview.png")){ return OSIMOPATH."os-content/themes/$theme/preview.png"; }
	elseif(file_exists(ABS_THEME_FOLDER."$theme/preview.gif")){ return OSIMOPATH."os-content/themes/$theme/preview.gif"; }
	else{ return OSIMOPATH."os-admin/img/nothemepreview.png"; }
}

function admin_getSmileyPreview($name)
{
	$smileyfolder = ABS_THEME_FOLDER.'../smilies/'.$name.'/';
	$count = 0;
	
	$dh = opendir($smileyfolder);
	while (false !== ($smiley = readdir($dh))) {
        if(is_file($smileyfolder.$smiley)&&$smiley!='.'&&$smiley!='..'&&$count<5)
        {
        	$preview .= "<img src=\"".OSIMOPATH."os-content/smilies/$name/$smiley\" alt=\"smiley\" />";
        	$count++;
        }
    }
    closedir($dh);
    
    return $preview;
}

function admin_getActiveSmilies()
{
	$query = "SELECT value FROM config WHERE name='current-smilies' LIMIT 1";
	$result = mysql_query($query);
	if($result){ $name = reset(mysql_fetch_row($result)); }
	$query2 = "SELECT code,image FROM smilies WHERE smileySet='$name'";
	$result2 = mysql_query($query2);
	if($result2)
	{
		$i=0;
		while(list($code,$image)=mysql_fetch_row($result2))
		{
			$imageName[$i] = $image;
			$codeName[$i] = $code;
			$i++;
		}
	}
	
	$smileyfolder = ABS_THEME_FOLDER.'../smilies/'.$name.'/';
	
	$dh = opendir($smileyfolder);
	$j=0;
	while (false !== ($smiley = readdir($dh))) {
        if(is_file($smileyfolder.$smiley)&&$smiley!='.'&&$smiley!='..')
        {
        	$smilies[$j]['fileName'] = $smiley;
        	$smilies[$j]['imgURL'] = "<img src=\"".OSIMOPATH."os-content/smilies/$name/$smiley\" alt=\"smiley\" />";
        	$key = array_search($smiley,$imageName);
        	$smilies[$j]['code'] = $codeName[$key];
        	$j++;
        }
    }
    closedir($dh);
    
    return $smilies;
}

function admin_getActiveSmileyName()
{
	$query = "SELECT value FROM config WHERE name='current-smilies' LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		return ucwords(str_replace("-"," ",reset(mysql_fetch_row($result))));
	}
	
	return false;
}

function admin_getThemeInfo($theme)
{
	if(file_exists(ABS_THEME_FOLDER."$theme/info.txt"))
	{
		$handle = fopen(ABS_THEME_FOLDER."$theme/info.txt",'r');
		$data = fread($handle,filesize(ABS_THEME_FOLDER."$theme/info.txt"));
		fclose($handle);
		
		$info = explode("\n",$data);
		
		for($i=0;$i<count($info);$i++)
		{
			if(strpos($info[$i],"title:")!==false)
			{
				$themeInfo['title'] = trim(str_replace("title:","",$info[$i]));
			}
			if(strpos($info[$i],"author:")!==false)
			{
				$themeInfo['author'] = trim(str_replace("author:","",$info[$i]));
			}
			if(strpos($info[$i],"description:")!==false)
			{
				$themeInfo['description'] = trim(str_replace("description:","",$info[$i]));
			}
			if(strpos($info[$i],"version:")!==false)
			{
				$themeInfo['version'] = trim(str_replace("version:","",$info[$i]));
			}
			if(strpos($info[$i],"release date:")!==false)
			{
				$themeInfo['release'] = trim(str_replace("release date:","",$info[$i]));
			}
			if(strpos($info[$i],"other info:")!==false)
			{
				$themeInfo['other'] = trim(str_replace("other info:","",$info[$i]));
			}
		}
		
		if(!isset($themeInfo['title'])){ $themeInfo['title'] = ucwords(str_replace("-"," ",$theme)); }
		if(!isset($themeInfo['author'])){ $themeInfo['author'] = "Unknown"; }
		if(!isset($themeInfo['description'])){ $themeInfo['description'] = "None"; }
		if(!isset($themeInfo['version'])){ $themeInfo['version'] = "Unknown"; }
		if(!isset($themeInfo['release'])){ $themeInfo['release'] = "Unknown"; }
		if(!isset($themeInfo['other'])){ $themeInfo['other'] = "None"; }
		
		return $themeInfo;
	}
	else
	{
		return false;
	}
}
?>