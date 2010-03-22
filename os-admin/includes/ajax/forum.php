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
if(isset($_POST['refreshTree'])){
	include_once('../forum.php');
	$forums = $admin->getForumList('all=true');
	$data = parseForums($forums);
	outputForums($data,-1);
}
if(isset($_POST['updateForum'])){ updateForumStructure($_POST['forumID'],$_POST['category']); }
if(isset($_POST['addCategory'])){ addCategory($_POST['name'],$_POST['parent']); }
if(isset($_POST['addForum'])){ addForum($_POST['name'],$_POST['parent'],$_POST['description']); }
if(isset($_POST['refreshForumDropDown'])){ outputForumDropDown(); }
if(isset($_POST['refreshCategoryDropDown'])){ outputCategoryDropDown(); }

function parseForums($forums)
{	
	$query = "SELECT id,parent_forum FROM category";
	$result = mysql_query($query);
	while(list($id,$parent_forum)=mysql_fetch_row($result))
	{
		$categories[$id] = $parent_forum;
	}
	
	$usedCats = array();
	if(is_array($forums))
	{
		$data = array();
		foreach($forums as $catID=>$count)
		{
			$usedCats[] = $catID;
			foreach($count as $forum)
			{
				$data[$forum['parent_forum']][$catID][$forum['id']] = $forum['title'];
			}
		}
	}
	
	if(is_array($categories))
	{
		$catIDs = array_keys($categories);
		$notUsed = array_diff($catIDs,$usedCats);
		foreach($categories as $catID=>$parent_forum)
		{
			if(in_array($catID,$notUsed))
			{
				$data[$parent_forum][$catID] = null;
			}
		}
	}
	
	return $data;
}

function outputForums($forums,$parent)
{
	global $admin,$osimo;
	
	foreach($forums as $parentForum=>$temp1)
	{
		if($parentForum==$parent)
		{
			echo "<ul class=\"parent_$parentForum\">\n";
			foreach($temp1 as $catID=>$temp2)
			{
				$catName = $osimo->getCategoryName("id=$catID");
				echo "<li id=\"category_$catID\" class=\"category drop-box\">";
				echo "<img src=\"img/icons/folder.png\" alt=\"Category\" title=\"Category\" />";
				echo " $catName</li>\n";
				echo "<ul id=\"forumlist_$catID\">\n";
				if(is_array($temp2))
				{
					foreach($temp2 as $forumID=>$forumTitle)
					{
						echo "<li id=\"forum_$forumID\" class=\"forum\">";
						echo "<img src=\"img/icons/page.png\" alt=\"Forum\" title=\"Forum\" />";
						echo " $forumTitle</li>\n";
						outputForums($forums,$forumID);
					}
				}
					
				echo "</ul>\n";
			}
			echo "</ul>\n";
		}
		
	}
}

function updateForumStructure($forumID,$category)
{
	$forumID = secureContent($forumID);
	$category = secureContent($category);
	
	$query = "UPDATE forum SET category='$category',parent_forum=(SELECT parent_forum FROM category WHERE id='$category' LIMIT 1) WHERE id='$forumID' LIMIT 1";
	$result = mysql_query($query);
	
	if($result){ echo "1"; }
	else{ echo "0"; }
}

function outputForumDropDown()
{
	global $admin;
	$forums = $admin->getForumNames();
	echo "<option value=\"-1\">None</option>\n";
	foreach($forums as $forum)
	{
	    echo "<option value=\"{$forum['id']}\">{$forum['title']}</option>\n";
	}
}

function outputCategoryDropDown()
{
	global $admin,$categories;
	if(!isset($categories))
	{
		$categories = $admin->getCategoryList();
	}
	
	foreach($categories as $catID=>$category)
	{
		echo "<option value=\"{$catID}\">{$category['title']}</option>\n";
	}
}

function addCategory($name,$parent)
{
	$name = secureContent($name);
	$parent = secureContent($parent);
	
	$query = "INSERT INTO category (title,parent_forum) VALUES ('$name','$parent')";
	$result = mysql_query($query);
	
	if($result){ echo "1"; }
	else{ echo "0"; }
}

function addForum($name,$category,$description)
{
	$name = secureContent($name);
	$category = secureContent(stripslashes($category));
	$description = secureContent(stripslashes($description));
	
	$query = "INSERT INTO forum (category,parent_forum,title,description) VALUES ('$category',(SELECT parent_forum FROM category WHERE id='$category' LIMIT 1),'$name','$description')";
	$result = mysql_query($query);
	
	if($result)
	{
		echo "1";
	}
	else
	{
		echo "0";
	}
}
?>