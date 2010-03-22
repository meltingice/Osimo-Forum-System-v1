<?php
/**
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/osimo.php - this is where the magic happens
*	Use this class to access most Osimo functions
*/
include_once(ABS_INCLUDES.'security.php');
include_once(ABS_INCLUDES.'utilities.php');
include_once(ABS_INCLUDES.'adodb-time.inc.php');
include_once(ABS_INCLUDES.'cache.php');

class Osimo
{	
	public $cache;
	/**	Begins the users Osimo session by including important files
	*	and running some initialization functions.
	*/
	function Osimo()
	{
		$this->cache = new OsimoCache();
		$this->cache->debug(false);
		$this->checkForLogin();
		$this->setPermissions();
		$this->setOptions();
		$this->setUserGroups();
	}
	
	/**	Retrieves a list of forums based on passed parameters.
	*	@param OQF Variable number of strings in Osimo Query Format
	*	@return Array of forums
	*/
	function getForumList()
	{
		include_once(ABS_INCLUDES.'forum.php');
		if(func_num_args()>0){ $args = $this->processArgs(func_get_args()); }
		else{ $args = null; }
		
		$forums = _getForumList($args);
		return $forums;
	}
	
	function getParentForum()
	{
		if(isset($_GET['id'])&&is_numeric($_GET['id']))
		{
			return secureContent($_GET['id']);
		}
		else
		{
			return -1;
		}
	}
	
	function getForumName($forumID)
	{
		include_once(ABS_INCLUDES.'forum.php');
		
		return _getForumName($forumID);
	}
	
	function getCategories($forums)
	{
		return array_keys($forums);
	}
	
	function getCategoryName()
	{
		include_once(ABS_INCLUDES.'forum.php');
		if(func_num_args()>0){ $args = $this->processArgs(func_get_args()); }
		else{ $args = null; }
		
		$category = _getCategoryName($args);
		return $category;
	}
	
	function getThreadList()
	{
		include_once(ABS_INCLUDES.'thread.php');
		if(func_num_args()>0){ $args = $this->processArgs(func_get_args()); }
		else{ $args = null; }
		
		if(array_key_exists('num',$args))
		{
			$num = $args['num'];
			$args['num'] = false;
		}
		else
		{
			$num = -1;
		}
		if(array_key_exists('page',$args))
		{
			$page = $args['page'];
			$args['page'] = false;
		}
		else
		{
			$page = 1;
		}
		if(array_key_exists('sticky',$args))
		{
			$sticky = $args['sticky'];
			$args['sticky'] = false;
		}
		else
		{
			$sticky = 'false';
		}
		$args = array_filter($args);
		
		return _getThreadList($num,$page,$sticky,$args);
	}
	
	function getRecentlyUpdatedThreads($num)
	{
		include_once(ABS_INCLUDES.'thread.php');
		
		return _getRecentlyUpdatedThreads($num);
	}
	
	function getThreadID()
	{
		if(isset($_GET['id'])&&is_numeric($_GET['id']))
		{
			return secureContent($_GET['id']);
		}
		else
		{
			return -1;
		}
	}
	
	function getThreadName($threadID)
	{
		include_once(ABS_INCLUDES.'thread.php');
		
		return _getThreadName($threadID);
	}
	
	function getPostList()
	{
		include_once(ABS_INCLUDES.'post.php');
		if(func_num_args()>0){ $args = $this->processArgs(func_get_args()); }
		
		if(array_key_exists('num',$args))
		{
			$num = $args['num'];
			$args['num'] = false;
		}
		else
		{
			$num = 10;
		}
		if(array_key_exists('page',$args))
		{
			$page = $args['page'];
			$args['page'] = false;
		}
		else
		{
			$page = 1;
		}
		$args = array_filter($args);
		return _getPostList($num,$page,$args);
	}
	
	function getPostLocation($post_id){
		include_once(ABS_INCLUDES.'post.php');
		
		return _getPostLocation($post_id);
	}
	
	function getUserProfileInfo($id=-1)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		if($id==-1&&isset($_GET['id'])&&is_numeric($_GET['id']))
		{
			$id = $_GET['id'];
		}
		elseif($id=-1&&(!isset($_GET['id'])||!is_numeric($_GET['id'])))
		{
			if(isset($_SESSION['user']['ID']))
			{
				$id = $_SESSION['user']['ID'];
			}
			else
			{
				return false;
			}
		}
		else
		{
			/* Do nothing, we're all set! */
		}
		
		return _getUserProfileInfo($id);
	}
	
	function getNewestUser()
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _getNewestUser();
	}
	
	function getOnlineUsers($type=-1,$id=-1)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _getOnlineUsers($type,$id);
	}
	
	function isUserOnline($id)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _isUserOnline($id);
	}
	
	function getPostPermalink($postID,$threadID,$page)
	{
		include_once(ABS_INCLUDES.'post.php');
		
		return _getPostPermalink($postID,$threadID,$page);
	}
	
	function getThreadForum($threadID)
	{
		include_once(ABS_INCLUDES.'thread.php');
		
		return _getThreadForum($threadID);
	}
	
	function getPostThread($postID)
	{
		include_once(ABS_INCLUDES.'post.php');
		
		return _getPostThread($postID);
	}
	
	function areForums($forums)
	{
		return is_array($forums);
	}
	
	function areThreads($threads)
	{
		return is_array($threads);
	}
	
	function arePosts($posts)
	{
		return is_array($posts);
	}
	
	function areMessages($messages)
	{
		return is_array($messages);
	}
	
	function isSearch($search)
	{
		if(is_array($search)&&$_GET['type']=='content'){ return 'content'; }
		if(is_array($search)&&$_GET['type']=='postsby'){ return 'postsby'; }
		elseif($search == 'noresults'){ return 'noresults'; }
		elseif($search == 'tooshort'){ return 'tooshort'; }
		else{ return false; }
	}
	
	function isThreadSticky($threadID)
	{
		include_once(ABS_INCLUDES.'thread.php');
		return _isThreadSticky($threadID);
	}
	
	function isThreadLocked($threadID)
	{
		include_once(ABS_INCLUDES."thread.php");
		return _isThreadLocked($threadID);
	}
	
	function isThreadNew($threadID)
	{
		include_once(ABS_INCLUDES.'thread.php');
		return _isThreadNew($threadID);
	}
	
	function isForumNew($forumID)
	{
		include_once(ABS_INCLUDES.'forum.php');
		return _isForumNew($forumID);
	}
	
	function getForumLink($id,$title)
	{
		include_once(ABS_INCLUDES.'theme.php');
		return _getForumLink($id,$title);
	}
	
	function getThreadLink($id,$title)
	{
		include_once(ABS_INCLUDES.'theme.php');
		return _getThreadLink($id,$title);
	}
	
	function getMessageLink($id,$title)
	{
		include_once(ABS_INCLUDES.'theme.php');
		return _getMessageLink($id,$title);
	}
	
	function getEditPostLink($id,$title)
	{
		include_once(ABS_INCLUDES.'theme.php');
		return _getEditPostLink($id,$title);
	}
	
	function getHeader()
	{
		include_once(ABS_INCLUDES.'theme.php');
		echo _getHeader();
	}
	
	function getPagination()
	{
		if(func_num_args()>0){ $args = $this->processArgs(func_get_args()); }
		else{ $args = null; }
		
		if(isset($args['num']))
		{
			$num = $args['num'];
			$args['num'] = false;
		}
		else
		{
			$num = -1;
		}
		$table = $args['table'];
		$args['table'] = false;
		$args = array_filter($args);
		
		return _getPagination($table,$num,$args);
	}
	
	function outputPagination($numPages=10,$startPage=1)
	{
		include_once(ABS_INCLUDES.'theme.php');
		
		return _outputPagination($numPages,$startPage);
	}
	
	function getPresetPagination($page,$id,$activePage=1)
	{
		include_once(ABS_INCLUDES.'theme.php');
		
		return _getPresetPagination($page,$id,$activePage);
	}
	
	function getBreadcrumbTrail($type,$id=-1)
	{
		include_once(ABS_INCLUDES.'theme.php');
		
		if($id==-1&&isset($_GET['id'])&&is_numeric($_GET['id'])){ $id = $_GET['id']; }
		
		return _getBreadcrumbTrail($type,$id);
	}
	
	function getForumDropdown()
	{
		include_once(ABS_INCLUDES.'theme.php');
		
		return _getForumDropdown();
	}
	
	function getLoggedInUser()
	{
		if(isset($_SESSION['user']))
		{
			$user['ID'] = $_SESSION['user']['ID'];
			$user['name'] = $_SESSION['user']['name'];
			$user['display_name'] = $_SESSION['user']['display_name'];
    		$user['email'] = $_SESSION['user']['email'];
    		$user['time_zone'] = $_SESSION['user']['time_zone'];
		}
		else
		{
			return false;
		}
		
		return $user;
	}
	
	function setUserOnline()
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _setUserOnline();
	}
	
	function setUserGroups()
	{
		include_once(ABS_INCLUDES.'groups.php');
		
		_setUserGroups();
	}
	
	function getUserGroups()
	{
		include_once(ABS_INCLUDES.'groups.php');
		if(func_num_args()>0){ $args = $this->processArgs(func_get_args()); }
		else{ $args = null; }
		
		return _getUserGroups($args);
	}
	
	function getUserLocalTime($id=-1)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		if($id==-1&&isset($_SESSION['user']))
		{
			$id = $_SESSION['user']['ID'];
		}
		elseif($id==-1&&!isset($_SESSION['user']))
		{
			return time();
		}
		else
		{
			/* Do nothing */
		}
		
		return _getUserLocalTime($id);
	}
	
	function getUserSignature($id=-1,$formatted=true,$forceDB=false)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		if($id==-1&&isset($_SESSION['user']))
		{
			$id = $_SESSION['user']['ID'];
		}
		elseif($id==-1&&!isset($_SESSION['user']))
		{
			return false;
		}
		else
		{
			/* Do nothing */
		}
		
		return _getUserSignature($id,$formatted,$forceDB);
	}
	
	function getUserAvatar($id=-1,$formatted=true)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _getUserAvatar($id,$formatted);
	}
	
	function getUserInfo($id,$data)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _getUserInfo($id,$data);
	}
	
	function userIDtoName($userID)
	{
		include_once(ABS_INCLUDES."user.php");
		
		return _userIDtoName($userID);
	}
	
	function userNametoID($username)
	{
		include_once(ABS_INCLUDES."user.php");
		
		return _userNametoID($username);
	}
	
	/*
	*	Permission Functions
	*/
	function setPermissions()
	{
		include_once(ABS_INCLUDES.'groups.php');
		
		return _setPermissions();
	}
	
	function userCanViewForums()
	{
		return $_SESSION['osimo']['perms']['global']['view_forum'];
	}
	
	function userIsAdmin()
	{
		return $_SESSION['osimo']['perms']['global']['admin'];
	}
	
	function userIsModerator($forumID=-1)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['moderate_forum'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['global_mod'];
		}
	}
	
	function userCanViewForum($forumID)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['view_forum'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['view_forum'];
		}
	}
	
	function userCanViewThreads($forumID=-1)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['view_thread'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['view_thread'];
		}
	}
	
	function userCanPostThread($forumID=-1)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['post_thread'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['post_thread'];
		}
	}
	
	function userCanPostReply($forumID=-1)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['post_reply'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['post_reply'];
		}
	}
	
	function userCanPostLinks($forumID=-1)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['post_links'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['post_links'];
		}
	}
	
	function userCanEditPosts($forumID=-1)
	{
		if($this->userIsAdmin()){ return true; }
		else if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['edit'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['edit'];
		}
	}
	
	function userCanCreatePoll($forumID=-1)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['create_poll'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['create_poll'];
		}
	}
	
	function userCanVote($forumID=-1)
	{
		if(isset($_SESSION['osimo']['perms']['spec'])&&in_array($forumID,array_keys($_SESSION['osimo']['perms']['spec'])))
		{
			return $_SESSION['osimo']['perms']['spec'][$forumID]['vote'];
		}
		else
		{
			return $_SESSION['osimo']['perms']['global']['vote'];
		}
	}
	
	function userCanSendPM()
	{
		return $_SESSION['osimo']['perms']['global']['send_pm'];
	}
	
	function userCanReceivePM()
	{
		return $_SESSION['osimo']['perms']['global']['receive_pm'];
	}
	
	function userRecieveAlert()
	{
		return $_SESSION['osimo']['perms']['global']['receive_alert'];
	}
	
	function userCanViewProfiles()
	{
		return $_SESSION['osimo']['perms']['global']['view_profile'];
	}
	
	function userCanEditProfile()
	{
		$_SESSION['osimo']['perms']['global']['edit_profile'];
	}
	
	function userIsPostOwner($id)
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _userIsPostOwner($id);
	}
	
	/* End permission functions */
	
	/*
	*	Feature Functions
	*/
	
	function updateRank($user_id, $special_rank = 0)
	{
		include_once(ABS_INCLUDES."theme.php");
		
		return _updateRank($user_id, $special_rank);
	}
	
	function outputRank($user_id)
	{
		include_once(ABS_INCLUDES."theme.php");
		
		return _outputRank($user_id);
	}
	
	function outputUsername($user_id)
	{
		include_once(ABS_INCLUDES."theme.php");
		
		return _outputUsername($user_id);
	}
	
	function getActiveSmilies()
	{
		include_once(ABS_INCLUDES.'theme.php');
		
		return _getActiveSmilies();
	}
	
	function createWarning($user_id, $post_id = 0)
	{
		include_once(ABS_INCLUDES."user.php");
		
		return _createWarning($user_id, $post_id);
	}
	
	function getUserWarnings($user_id)
	{
		include_once(ABS_INCLUDES."user.php");
		
		return _getUserWarnings($user_id);
	}
	
	function allowLogin($user_id)
	{
		include_once(ABS_INCLUDES."user.php");
		
		return _allowLogin($user_id);
	}
	
	function getMemberList($page,$rows=25,$sort='id',$sortDir='ASC')
	{
		include_once(ABS_INCLUDES."memberlist.php");
		
		return _getMemberList($page,$rows,$sort,$sortDir);
	}
	
	/* End Feature Functions */
	
	/* Private Messaging Functions */
	function getPrivateMessageThreads($which='inbox')
	{
		include_once(ABS_INCLUDES."messages.php");
		
		return _getPrivateMessageThreads($which);
	}
	
	function getPrivateMessagePosts($id=false)
	{
		if(isset($_GET['id'])&&is_numeric($_GET['id']))
		{
			include_once(ABS_INCLUDES."messages.php");
			return _getPrivateMessagePosts($_GET['id']);
		}
		elseif($id)
		{
			include_once(ABS_INCLUDES."messages.php");
			return _getPrivateMessagePosts($id);
		}
		else
		{
			return false;
		}
	}
	
	function getMessageTitle($id=-1)
	{
		if($id==-1&&is_numeric($_GET['id']))
		{
			include_once(ABS_INCLUDES.'messages.php');
			return _getMessageTitle($_GET['id']);
		}
		else
		{
			include_once(ABS_INCLUDES.'messages.php');
			return _getMessageTitle($id);
		}
		
		return false;
	}
	
	function getNumUnreadMessages()
	{
		include_once(ABS_INCLUDES."messages.php");
		
		return _getNumUnreadMessages();
	}
	
	function markMessageAsRead($id=-1)
	{
		include_once(ABS_INCLUDES.'messages.php');
		
		if($id==-1&&isset($_GET['id'])&&is_numeric($_GET['id']))
		{
			$id = $_GET['id'];
		}
		
		return _markMessageAsRead($id);
	}
	
	function getMessageUsers($id=-1)
	{
		include_once(ABS_INCLUDES.'messages.php');
		
		if($id==-1&&isset($_GET['id'])&&is_numeric($_GET['id']))
		{
			$id = $_GET['id'];
		}
		
		return _getMessageUsers($id);
	}
	/* End Private Messaging Functions */
	
	/* Navigation Functions */
	function getNavigationList($panel="header")
	{
		include_once(ABS_INCLUDES."theme.php");
		
		return _getNavigationList($panel);
	}
	
	function outputUnorderedList($array)
	{
		include_once(ABS_INCLUDES."theme.php");
		
		return _outputUnorderedList($array);
	}
	/* End navigation functions */
	
	/* Option retrieval */
	function setOptions()
	{
		include_once(ABS_INCLUDES.'options.php');
		
		_setOptions();
	}
	
	function option_threadNumPerPage()
	{
		include_once(ABS_INCLUDES.'options.php');
		
		return _threadNumPerPage();
	}
	
	function option_postNumPerPage()
	{
		include_once(ABS_INCLUDES.'options.php');
		
		return _postNumPerPage();
	}
	
	function getSiteTitle()
	{
		include_once(ABS_INCLUDES.'options.php');
		
		return _getSiteTitle();
	}
	
	function getSiteDescription()
	{
		include_once(ABS_INCLUDES.'options.php');
		
		return _getSiteDescription();
	}
	
	/* End option retrieval */
	
	function getTodayTimestamp()
	{
		include_once(ABS_INCLUDES.'utilities.php');
		
		return _getTodayTimestamp();
	}
	
	function addPageView($type,$id)
	{
		include_once(ABS_INCLUDES.'stats.php');
		
		return _addPageView($type,$id);
	}
	
	function getCurrentTheme($forceDB=false)
	{
		include_once(ABS_INCLUDES.'theme.php');
		
		return _getCurrentTheme($forceDB);
	}
	
	function checkForLogin()
	{
		include_once(ABS_INCLUDES.'user.php');
		
		return _checkForLogin();
	}
	
	function getAlertMsg()
	{
		if($_GET['login']=='passfail'){ return "The password entered is incorrect."; }
		elseif($_GET['login']=='userfail'){ return "This username does not exist."; }
		elseif($_GET['login']=='success'){ return "You have been logged in."; }
		elseif($_GET['register']=='missingdata'){ return "You did not fill out all required fields."; }
		elseif($_GET['register']=='passmismatch'){ return "The passwords entered do not match."; }
		elseif($_GET['register']=='emailmismatch'){ return "The email addresses entered do not match."; }
		elseif($_GET['register']=='invalidemail'){ return "The email address entered is not valid."; }
		elseif($_GET['register']=='usernameinvalid'){ return "The username entered is not valid"; }
		elseif($_GET['register']=='usertaken'){ return "The username entered is already taken"; }
		elseif($_GET['register']=='success'){ return "You have been sucessfully registered."; }
		elseif($_GET['register']=='fail'){ return "Registration failed."; }
		elseif($_GET['logout']=='true'){ return "You have been logged out."; }
		else{ return false; }
	}
	
	function floodControl($type)
	{
		include_once('utilities.php');
		
		return _floodControl($type);
	}
	
	function getSearchResults($query,$type,$page=1,$textColor=false,$bgColor=false)
	{
		include_once(ABS_INCLUDES.'search.php');
		
		return _getSearchResults($query,$type,$page,$textColor,$bgColor);
	}
	
	function getNumSearchPages($query,$type)
	{
		include_once(ABS_INCLUDES.'search.php');
		
		return _getNumSearchPages($query,$type);
	}
	
	function getSearchPresetPagination($numPages)
	{
		include_once(ABS_INCLUDES.'search.php');
		
		return _getSearchPresetPagination($numPages);
	}
	
	function writeToSysLog($type,$user,$message)
	{
		return _writeToSysLog($type,$user,$message);
	}
	
	function getUserTime($timestamp=false)
	{
		return _getUserTime($timestamp);
	}
	
	function getUsernameColor($userID=-1)
	{
		include_once(ABS_INCLUDES."user.php");
		
		return _getUsernameColor($userID);
	}
	
	function numUserReports(){
		include_once(ABS_INCLUDES."user.php");
		
		return _numUserReports();
	}
	
	/*
	*	processArgs() - used for processing an argument list from func_get_args()
	*	This allows for easy override of default values for function arguements.
	*	returns associative array
	*	example function with arguements: function('var1=0','var2=cheese')
	*	returns:
	*	-> $args['var1'] = 0;
	*	-> $args['var2'] = cheese;
	*/
	function processArgs($args)
	{
		$argArray = array();
		if(is_array($args)&&count($args)>0)
		{
			foreach($args as $arg)
			{
				$temp = explode('=',$arg);
				$argArray[$temp[0]] = $temp[1];
			}
		}
		else
		{
			$temp = explode('=',$args);
			$argArray[$temp[0]] = $temp[1];
		}
		
		return $argArray;
	}
}
?>