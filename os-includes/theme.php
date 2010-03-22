<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/theme.php - general theme related helper functions
*	Functions should be accessed through the Osimo class
*/

function _getHeader()
{
	global $osimo,$page,$threadID,$PM;
	$title = _getHeaderTitle();
	$header = "<title>$title</title>\n";
	if($threadID&&!$PM)
	{
		$header .= "
			<meta http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\" />
		";
		$header .= "
		<script>
			function ajaxBookmark()
			{
				var curUrl = new String(window.location);
				if(curUrl.indexOf('#')==-1)
				{
					return false;
				}
				else
				{
					var temp = curUrl.split('#');
					
					var splitUrl = temp[1].split('&');
					if(splitUrl.length==1&&splitUrl.indexOf('post')!=-1){ return temp[1]; }
					else
					{
						return splitUrl;
					}
				}
			}
			
			var bookmark = ajaxBookmark();
			for(i=0;i<bookmark.length;i++)
			{
				if(bookmark[i].indexOf('page')!=-1)
				{
					var pageTemp = bookmark[i].split('=');
					var newPage = pageTemp[1];
					
					var locTemp = String(window.location).split('#');
					if(locTemp[0].indexOf('&page')!=-1)
					{
						var locTemp2 = locTemp[0].split('&page=$page');
					}
					else
					{
						var locTemp2 = locTemp[0].split('?page=$page');
					}
					
					var curLoc = ''
					for(j=0;j<locTemp2.length;j++)
					{
						curLoc += locTemp2[j];
					}
					
					
					window.location.replace(curLoc+'&page='+newPage);
				}
			}
		</script>";
		$header .= "
		<script>
			curPostPage = $page;
			totPostPages = ".$osimo->getPagination('table=post',"thread=$threadID").";
		</script>";
	}
	elseif($page)
	{
		$header .= "<script>curThreadPage = $page;</script>\n";
	}
	$header .= "<script src=\"".JS_PATH."jquery.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";
	$header .= "<script src=\"".JS_PATH."jquery-ui.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";
	$header .= "<script src=\"".JS_PATH."jquery.autocomplete.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";
	$header .= "<script src=\"".JS_PATH."ajax.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";
	$header .= "<script src=\"".JS_PATH."backend.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";
	$header .= "<script src=\"".JS_PATH."osimo_editor/osimo_editor.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";
	$header .= '<script type="text/javascript" src="'.JS_PATH.'syntax/syntax.js" type=\"text/javascript\"></script>';
	$header .= "<link rel=\"stylesheet\" href=\"".CSS_PATH."jquery.autocomplete.css\" type=\"text/css\" media=\"screen\" title=\"no title\" charset=\"utf-8\" />\n";
	$header .= "<link rel=\"stylesheet\" href=\"".CSS_PATH."standard.css\" type=\"text/css\" media=\"screen\" title=\"no title\" charset=\"utf-8\" />\n";
	$header .= "<link rel=\"stylesheet\" href=\"".CSS_PATH."reset.css\" type=\"text/css\" media=\"screen\" title=\"no title\" charset=\"utf-8\" />\n";

	return $header;
}

function _getHeaderTitle()
{
	global $osimo,$pageType,$pageID;
	
	$siteTitle = $osimo->getSiteTitle();
	
	if($pageType=='forum')
	{
		$forum = $osimo->getForumName($pageID);
		return "$siteTitle > $forum - Powered by Osimo";
	}
	elseif($pageType=='index')
	{
		return "$siteTitle - Powered by Osimo";
	}
	elseif($pageType=='login')
	{
		return "$siteTitle > Login - Powered by Osimo";
	}
	elseif($pageType=='inbox')
	{
		$user = $osimo->getLoggedInUser();
		return "$siteTitle > {$user['name']}'s Inbox - Powered by Osimo";
	}
	elseif($pageType=='profile')
	{
		$user = $osimo->userIDtoName($pageID);
		return "$siteTitle > $user's Profile - Powered by Osimo";
	}
	elseif($pageType=='message')
	{
		$user = $osimo->getLoggedInUser();
		$title = $osimo->getMessageTitle($pageID);
		return "$siteTitle > {$user['name']}'s Inbox > $title - Powered by Osimo";
	}
	elseif($pageType=='register')
	{
		return "$siteTitle > Register - Powered by Osimo";
	}
	elseif($pageType=='thread')
	{
		$forum = $osimo->getThreadForum($pageID);
		$threadName = $osimo->getThreadName($pageID);
		return "$siteTitle > {$forum['title']} > $threadName - Powered by Osimo";
	}
	else
	{
		return "$siteTitle - Powered by Osimo";
	}
}

function _getForumLink($id,$title)
{
	$link = "<a href=\"forum.php?id=$id\">$title</a>";
	return $link;
}

function _getThreadLink($id,$title)
{
	$link = "<a href=\"thread.php?id=$id\">$title</a>";
	return $link;
}

function _getMessageLink($id,$title)
{
	$link = "<a href=\"readmessage.php?id=$id\">$title</a>";
	return $link;
}

function _getEditPostLink($id,$title)
{
	global $osimo;
	if(($osimo->userIsPostOwner($id)&&$osimo->userCanEditPosts())||$osimo->userIsModerator()||$osimo->userIsAdmin())
	{
		$link = "<a href=\"javascript:editPostBox($id)\">$title</a>";
	}
	else
	{
		$link = false;
	}
	
	return $link;
}

function _getNavigationList($panel)
{
	$query = "SELECT name, link FROM navigation WHERE nav_panel='$panel'";
	$result = mysql_query($query);
	
	while($fetch = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$list[$fetch['name']] = $fetch['link'];
	}
	
	return $list;
}

function _outputUnorderedList($array)
{
	if(is_array($array))
	{
		$html .= "<ul>";
		
		foreach($array as $key => $val)
		{
			$html .= "
			<li><a href='" . $key . "'>" . $val . "</a></li>";
		}
	
		$html .= "</ul>";
		
		return $html;
	}
	
	return "";
}

function _getBreadcrumbTrail($type,$id)
{
	global $osimo;
	
	$id = secureContent($id);
	$run = true;
	$breadcrumbs = array();
	
	/* Gotta do a little more work this time */
	if($type=='thread')
	{
		$temp = $osimo->getThreadForum($id);
		$id = $temp['id'];
	}
	
	$breadcrumbs[0]['id'] = $id;
	$breadcrumbs[0]['name'] = $osimo->getForumName($id);
	$i=1;
	while($run)
	{
		$query = "SELECT parent_forum FROM forum WHERE id='$id' LIMIT 1";
		$result = mysql_query($query);
		if($result&&mysql_num_rows($result)>0)
		{
			$id = reset(mysql_fetch_row($result));
			if($id==-1){ $run = false; }
			else
			{
				$breadcrumbs[$i]['id'] = $id;
				$breadcrumbs[$i]['name'] = $osimo->getForumName($id);
				$i++;
			}
		}
	}
	
	return array_reverse($breadcrumbs);
}

function _getCurrentTheme($forceDB)
{
	if(!$forceDB)
	{
		if(isset($_SESSION['osimo']['options']['theme']))
		{
			return $_SESSION['osimo']['options']['theme'];
		}
	}
	include_once('dbconnect.php');
	$query = "SELECT value FROM config where name='current_theme' LIMIT 1";
	$result = mysql_query($query);
	
	if($result&&mysql_num_rows($result)>0)
	{
		$_SESSION['osimo']['options']['theme'] = reset(mysql_fetch_row($result));
		return $_SESSION['osimo']['options']['theme'];
	}
	else{ return 'default-theme'; }
}

/* Start Smiley Functions */
function _getActiveSmilies()
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
        	$key = array_search($smiley,$imageName);
        	if($codeName[$key]!=":unset:")
        	{
        		$smilies[$j]['fileName'] = $smiley;
        		$smilies[$j]['imgURL'] = "<img src=\"".OSIMOPATH."os-content/smilies/$name/$smiley\" alt=\"smiley\" />";
        		$smilies[$j]['code'] = $codeName[$key];
        		$j++;
        	}
        }
    }
    closedir($dh);
    
    return $smilies;
}
/* End Smiley Functions */

/* Start Ranks Functions */

function _outputUsername($user_id)
{
	# Gather User
	$query = "SELECT username, user_style, username_color FROM users WHERE id='" . $user_id . "'";
	$result = mysql_query($query);
	
	if($fetch_user = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$style = "";
		
		if($fetch_user['username_color'])
		{
			$style .= "color:#" . $fetch_user['username_color'] . ";";
		}
		
		if($fetch_user['user_style'])
		{
			$style .= $fetch_user['user_style'];
		}
		
		if($style)
		{
			$style = " style='$style'";
		}
		
		$html .= "<span" . $style . ">" . $fetch_user['username'] . "</span>";
		
		return $html;
	}
	
	return "";
}

function _outputRank($user_id)
{
	# Gather User
	$query = "SELECT user_style, username_color, rank_image, rank_status FROM users WHERE id='" . $user_id . "'";
	$result = mysql_query($query);
	
	if($fetch_user = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$style = "";
		
		if($fetch_user['username_color'])
		{
			$style .= "color:#" . $fetch_user['username_color'] . ";";
		}
		
		if($fetch_user['user_style'])
		{
			$style .= $fetch_user['user_style'];
		}
		
		if($style)
		{
			$style = " style='$style'";
		}
		
		$html .= "<span" . $style . ">" . $fetch_user['rank_status'] . "</span>";
		
		return $html;
	}
	
	return "";
}

function _updateRank($user_id, $special_rank = 0)
{
	# Gather User Posts
	$query = "SELECT id, rank_special, posts FROM users WHERE id='" . ($user_id + 0) . "'";
	$result = mysql_query($query);
	
	if($fetch_user = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		# Check if User's Rank is Special
		if($fetch_user['rank_special'] == 0)
		{
			# Gather Rank Info
			$query = "SELECT image, level, status, username_style, username_color, special_rank FROM ranks WHERE required_posts >= '" . $fetch_user['posts'] . "' ORDER BY required_posts ASC";
		}
		else
		{
			$query = "SELECT image, level, status, username_style, username_color, special_rank FROM ranks WHERE id = '" . ($special_rank + 0) . "'";
		}
		
		$result = mysql_query($query);
		
		if($fetch_rank = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			# Update User
			$query = "
				UPDATE users
					SET
						username_style='" . $fetch_rank['username_style'] . "',
						username_color='" . $fetch_rank['username_color'] . "',
						rank_status='" . $fetch_rank['status'] . "',
						rank_level='" . $fetch_rank['level'] . "',
						rank_image='" . $fetch_rank['image'] . "',
						rank_special='" . $fetch_rank['special_rank'] . "'
					WHERE
						id='" . $fetch_user['id'] . "'";
			
			mysql_query($query);
			
			return 1;
		}
	}
	
	return 0;
}

/* End Ranks Functions */

function _outputPagination($numPages,$startPage)
{
	if($numPages<=5)
	{
		for($i=1;$i<=$numPages;$i++)
		{
			$pages[] = $i; 
		}
	}
	else
	{
		if($startPage>($numPages-3)){ $startPage = $numPages-3; }
		if($startPage<2){ $startPage = 2; }
		
		$pages [0] = 1;
		if($startPage==1){ $startPage=2; }
		for($i=$startPage;$i<($startPage+3);$i++)
		{
			$pages[] = $i;
		}
		$pages[] = $numPages;
	}
	
	return $pages;
}

function _getPresetPagination($page,$id,$activePage)
{
	global $osimo;
		
	if($page=='thread')
	{
		$numPages = $osimo->getPagination('table=post',"thread=$id");
		if($activePage=='last'){ $activePage = $numPages; }
		$pagination = $osimo->outputPagination($numPages,$activePage-1);
		$i=1;
		$numPages = count($pagination);
		foreach($pagination as $pageID)
		{
			if($i==$numPages&&$pagination[$numPages-1]>5){ echo "... "; }
			echo "<a href=\"javascript:loadPostPage($id,$pageID);changeActiveThreadPage($pageID)\"";
			if($pageID==$activePage){ echo " class=\"osimo-active-thread-page\""; }
			echo " id=\"osimo_pagenav-$pageID\">$pageID</a> ";
			if($i==1&&$pagination[$numPages-1]>5){ echo "... "; }
			$i++;
		}
	}
	if($page=='forum')
	{
		$numPages = $osimo->getPagination('table=thread',"forum=$id");
		if($activePage=='last'){ $activePage = $numPages; }
		$pagination = $osimo->outputPagination($numPages,$activePage-1);
		$i=1;
		$numPages = count($pagination);
		foreach($pagination as $pageID)
		{
			if($i==$numPages&&$pagination[$numPages-1]>5){ echo "... "; }
			echo "<a href=\"javascript:loadThreadPage($id,$pageID,true);changeActiveForumPage($pageID)\"";
			if($pageID==$activePage){ echo " class=\"osimo-active-forum-page\""; }
			echo " id=\"osimo_pagenav-$pageID\">$pageID</a> ";
			if($i==1&&$pagination[$numPages-1]>5){ echo "... "; }
			$i++;
		}
	}
}

function _getForumDropdown()
{
	$query = "SELECT id,title FROM forum ORDER BY title ASC";
	$result = mysql_query($query);
	if($result)
	{
		while($data = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			$forums[$data['id']] = $data['title'];
		}
	}
	?>
	<select id="osimo_forum_selector">
	<? foreach($forums as $id=>$forum): ?>
		<option value="<?=$id?>"><?=$forum?></option>
	<?
	endforeach;
	echo "</select>\n";
}
?>