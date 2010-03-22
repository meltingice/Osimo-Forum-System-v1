<?php
include_once('../os-includes/dbconnect.php');
include_once('../os-includes/security.php');

if(isset($_POST['step'])){
	if($_POST['step']=='database'){ runInstall($_POST['step'],false); }
	if($_POST['step']=='config')
	{
		$data['siteTitle'] = secureContent(stripslashes($_POST['siteTitle']));
		$data['siteDesc'] = secureContent(stripslashes($_POST['siteDesc']));
		$data['adminEmail'] = secureContent(stripslashes($_POST['adminEmail']));
		runInstall('config',$data);
	}
	if($_POST['step']=='admin')
	{
		$data['adminName'] = secureContent(stripslashes($_POST['adminName']));
		$data['adminPass'] = secureContent(stripslashes($_POST['adminPass']));
		runInstall('admin',$data);
	}
}

function runInstall($step,$data)
{
	if($step=='database')
	{
		/* First, define all the tables in the database */
		$query1 = "CREATE TABLE IF NOT EXISTS `alerts` (
			  `id` int(11) NOT NULL auto_increment,
			  `is_announcement` int(1) NOT NULL default '0',
			  `is_warning` int(1) NOT NULL default '0',
			  `user_id` int(8) NOT NULL default '0',
			  `title` varchar(32) NOT NULL default '',
			  `message` text NOT NULL,
			  `link` varchar(200) NOT NULL default '',
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$query2 = "CREATE TABLE IF NOT EXISTS `category` (
 			`id` int(11) NOT NULL auto_increment,
  			`title` varchar(64) NOT NULL default '',
  			`parent_forum` int(8) NOT NULL default '-1',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query3 = "CREATE TABLE IF NOT EXISTS `config` (
  			`id` int(11) NOT NULL auto_increment,
  			`name` varchar(32) NOT NULL default '',
  			`value` text NOT NULL,
  			PRIMARY KEY  (`id`),
  			KEY `name` (`name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query4 = "CREATE TABLE IF NOT EXISTS `forum` (
  			`id` int(11) NOT NULL auto_increment,
  			`category` int(11) NOT NULL default '0',
  			`parent_forum` int(11) NOT NULL default '0',
  			`title` varchar(64) NOT NULL default '',
  			`description` text NOT NULL,
  			`views` int(11) NOT NULL default '0',
  			`threads` int(8) NOT NULL default '0',
  			`posts` int(8) NOT NULL default '0',
  			`last_poster` varchar(24) NOT NULL default '',
  			`last_poster_id` int(8) NOT NULL default '0',
  			`last_post_time` int(11) NOT NULL default '0',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query5 = "CREATE TABLE IF NOT EXISTS `forum_permissions` (
  			`id` int(11) NOT NULL auto_increment,
  			`group` int(11) NOT NULL default '0',
  			`forum` int(11) NOT NULL default '0',
  			`perm_can_moderate_forum` int(1) NOT NULL default '0',
  			`perm_can_view_forum` int(1) NOT NULL default '0',
  			`perm_can_view_thread` int(1) NOT NULL default '0',
  			`perm_can_post_thread` int(1) NOT NULL default '0',
  			`perm_can_post_reply` int(1) NOT NULL default '0',
  			`perm_can_post_links` int(1) NOT NULL default '0',
  			`perm_can_edit` int(1) NOT NULL default '0',
  			`perm_can_create_poll` int(1) NOT NULL default '0',
  			`perm_can_vote` int(1) NOT NULL default '0',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query6 = "CREATE TABLE IF NOT EXISTS `groups` (
  			`id` int(11) NOT NULL auto_increment,
  			`name` varchar(48) NOT NULL default '',
  			`description` text NOT NULL,
  			`username_style` varchar(72) NOT NULL default '',
  			`username_color` varchar(6) NOT NULL default '',
  			`perm_can_view_forum` int(1) NOT NULL default '0',
  			`perm_can_view_thread` int(1) NOT NULL default '0',
  			`perm_can_post_thread` int(1) NOT NULL default '0',
  			`perm_can_post_reply` int(1) NOT NULL default '0',
  			`perm_can_post_links` int(1) NOT NULL default '0',
  			`perm_can_edit` int(1) NOT NULL default '0',
  			`perm_can_create_poll` int(1) NOT NULL default '0',
  			`perm_can_vote` int(1) NOT NULL default '0',
  			`perm_can_send_pm` int(1) NOT NULL default '0',
  			`perm_can_receive_pm` int(1) NOT NULL default '0',
  			`perm_can_receive_alert` int(1) NOT NULL default '0',
  			`perm_can_view_profile` int(1) NOT NULL default '0',
  			`perm_can_edit_profile` int(1) NOT NULL default '0',
  			PRIMARY KEY  (`id`),
  			UNIQUE KEY `name` (`name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query7 = "CREATE TABLE IF NOT EXISTS `mod_reports` (
  			`id` mediumint(8) unsigned NOT NULL auto_increment,
  			`type` enum('warning','ban','p_del','t_move','t_del','t_sticky','t_lock','general') NOT NULL,
  			`title` varchar(120) NOT NULL,
  			`report` text NOT NULL,
  			`filed_by_id` bigint(20) unsigned NOT NULL,
  			`filed_by` varchar(120) NOT NULL,
  			`filed_against_id` bigint(20) unsigned NOT NULL,
  			`filed_against` varchar(120) NOT NULL,
  			`concerning_id` bigint(20) unsigned NOT NULL default '0',
  			`date_filed` bigint(20) unsigned NOT NULL,
  			`last_edit_by` mediumint(8) unsigned NOT NULL,
  			`last_edit_time` bigint(20) unsigned NOT NULL,
  			PRIMARY KEY  (`id`),
  			KEY `type` (`type`,`date_filed`),
  			KEY `filed_against` (`filed_against`),
  			KEY `concerning_post` (`concerning_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$query8 = "CREATE TABLE IF NOT EXISTS `mod_report_templates` (
  			`id` mediumint(8) unsigned NOT NULL auto_increment,
  			`type` enum('warning','ban','p_del','t_move','t_del','t_sticky','t_lock','general') NOT NULL,
  			`title` varchar(120) NOT NULL,
  			`report` text NOT NULL,
  			`created_by` mediumint(8) unsigned NOT NULL,
  			`date_created` bigint(20) unsigned NOT NULL,
  			PRIMARY KEY  (`id`),
  			KEY `type` (`type`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$query9 = "CREATE TABLE IF NOT EXISTS `navigation` (
  			`id` int(11) NOT NULL auto_increment,
 			`nav_panel` varchar(12) NOT NULL default '',
  			`name` varchar(24) NOT NULL default '',
  			`link` varchar(72) NOT NULL default '',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$query10 = "CREATE TABLE IF NOT EXISTS `post` (
  			`id` int(11) NOT NULL auto_increment,
  			`thread` int(11) NOT NULL default '0',
  			`body` text NOT NULL,
  			`poster_id` int(8) NOT NULL default '0',
  			`poster_username` varchar(24) NOT NULL default '',
  			`post_time` int(11) NOT NULL default '0',
  			`last_edit_user_id` int(8) NOT NULL default '0',
  			`last_edit_username` varchar(120) NOT NULL default '',
  			`last_edit_time` int(11) NOT NULL default '0',
  			PRIMARY KEY  (`id`), FULLTEXT (body)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query11 = "CREATE TABLE IF NOT EXISTS `private_message_post` (
  			`id` int(11) NOT NULL auto_increment,
  			`private_message_thread` int(11) NOT NULL default '0',
  			`body` text NOT NULL,
  			`poster_id` int(8) NOT NULL default '0',
  			`poster_username` varchar(24) NOT NULL default '',
  			`post_time` int(11) NOT NULL default '0',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query12 = "CREATE TABLE IF NOT EXISTS `private_message_thread` (
  			`id` int(11) NOT NULL auto_increment,
  			`user_sent` int(8) NOT NULL default '0',
  			`user_received` int(8) NOT NULL default '0',
  			`title` varchar(32) NOT NULL default '',
  			`views` int(11) NOT NULL default '0',
  			`posts` int(8) NOT NULL default '0',
  			`time_created` int(11) NOT NULL default '0',
  			`last_poster` varchar(24) NOT NULL default '',
  			`last_poster_id` int(8) NOT NULL default '0',
  			`last_post_time` int(11) NOT NULL default '0',
  			`read_status` enum('read','unread') NOT NULL default 'unread',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query13 = "CREATE TABLE IF NOT EXISTS `ranks` (
  			`id` int(8) NOT NULL auto_increment,
  			`image` varchar(96) NOT NULL default '',
  			`level` int(6) NOT NULL default '0',
  			`status` varchar(48) NOT NULL default '',
  			`username_style` varchar(72) NOT NULL default '',
  			`username_color` varchar(6) NOT NULL default '',
  			`required_posts` int(8) NOT NULL default '0',
  			`special_rank` int(1) NOT NULL default '0',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$query14 = "
			CREATE TABLE IF NOT EXISTS `smilies` (
			  `id` int(11) NOT NULL auto_increment,
			  `smileySet` varchar(120) NOT NULL default '',
			  `code` varchar(32) NOT NULL default '',
			  `image` varchar(72) NOT NULL default '',
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$query15 = "CREATE TABLE IF NOT EXISTS `stats` (
  			`id` bigint(20) unsigned NOT NULL auto_increment,
  			`forumID` mediumint(8) unsigned NOT NULL default '0',
  			`date` int(16) unsigned NOT NULL default '0',
  			`type` varchar(7) NOT NULL default '0',
  			`count` bigint(20) unsigned NOT NULL default '0',
 			PRIMARY KEY  (`id`),
  			KEY `date` (`date`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query16 = "CREATE TABLE IF NOT EXISTS `syslog` (
 			 `id` bigint(20) unsigned NOT NULL auto_increment,
 			 `time` bigint(20) unsigned NOT NULL default '0',
 			 `type` varchar(30) NOT NULL default '',
 			 `user` bigint(20) NOT NULL default '0',
 			 `message` text NOT NULL,
 			 PRIMARY KEY  (`id`),
 			 KEY `user` (`user`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$query17 = "CREATE TABLE IF NOT EXISTS `thread` (
  			`id` int(11) NOT NULL auto_increment,
  			`forum` int(11) NOT NULL default '0',
  			`title` varchar(64) NOT NULL default '',
  			`description` text NOT NULL,
  			`views` int(11) NOT NULL default '0',
  			`posts` int(8) NOT NULL default '0',
  			`original_poster` varchar(24) NOT NULL default '',
  			`original_poster_id` int(8) NOT NULL default '0',
  			`original_post_time` int(11) NOT NULL default '0',
  			`last_poster` varchar(24) NOT NULL default '',
  			`last_poster_id` int(8) NOT NULL default '0',
  			`last_post_time` int(11) NOT NULL default '0',
  			`sticky` tinyint(1) NOT NULL default '0',
  			`locked` tinyint(1) NOT NULL default '0',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query18 = "CREATE TABLE IF NOT EXISTS `users` (
  			`id` int(8) NOT NULL auto_increment,
  			`username` varchar(24) NOT NULL default '',
  			`username_clean` varchar(24) NOT NULL default '',
  			`email` varchar(48) NOT NULL default '',
  			`password` varchar(120) NOT NULL default '',
  			`ip_address` varchar(16) NOT NULL default '',
  			`birthday` int(11) NOT NULL default '0',
  			`signature` text NOT NULL,
  			`username_style` varchar(72) NOT NULL default '',
  			`username_color` varchar(6) NOT NULL default '',
  			`group_default` int(8) NOT NULL default '0',
  			`group_list` varchar(120) NOT NULL default '',
  			`rank_special` int(1) NOT NULL default '0',
  			`rank_level` int(6) NOT NULL default '0',
  			`rank_image` varchar(96) NOT NULL default '',
  			`rank_status` varchar(48) NOT NULL default '',
  			`posts` int(8) NOT NULL default '0',
  			`field_age` int(2) NOT NULL default '0',
  			`field_sex` varchar(6) NOT NULL default '',
  			`field_location` varchar(72) NOT NULL default '',
  			`field_aim` varchar(72) NOT NULL default '',
  			`field_jabber` varchar(72) NOT NULL default '',
  			`field_msn` varchar(72) NOT NULL default '',
  			`field_yim` varchar(72) NOT NULL default '',
  			`field_icq` varchar(72) NOT NULL default '',
  			`field_website` varchar(200) NOT NULL default '',
  			`field_about` text NOT NULL,
  			`field_interests` text NOT NULL,
  			`field_biography` text NOT NULL,
  			`is_confirmed` int(1) NOT NULL default '0',
  			`is_admin` int(1) NOT NULL default '0',
  			`is_global_mod` int(1) NOT NULL default '0',
  			`is_remembered` int(1) NOT NULL default '0',
  			`is_visible` int(1) NOT NULL default '0',
  			`is_mailing_list` int(1) NOT NULL default '0',
  			`is_accepting_messages` int(1) NOT NULL default '0',
  			`is_accepting_alerts` int(1) NOT NULL default '0',
  			`time_joined` int(11) NOT NULL default '0',
  			`time_last_visit` int(11) NOT NULL default '0',
  			`time_last_post` int(11) NOT NULL default '0',
  			`last_page` varchar(200) NOT NULL default '',
  			`last_page_type` enum('forum','thread','other','logoff') NOT NULL default 'forum',
  			`last_page_id` mediumint(8) unsigned NOT NULL default '0',
  			`time_zone` int(2) NOT NULL default '0',
  			`time_format` varchar(30) NOT NULL default 'n/j/Y g:ia',
  			`new_alert` int(1) NOT NULL default '0',
  			`new_message` int(1) NOT NULL default '0',
  			`reset_code` varchar(120) NOT NULL default '0',
  			PRIMARY KEY  (`id`),
  			UNIQUE KEY `username` (`username`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
		$query19 = "CREATE TABLE IF NOT EXISTS `warning` (
  			`id` int(11) NOT NULL auto_increment,
  			`user_id` int(8) NOT NULL default '0',
  			`post_id` int(11) NOT NULL default '0',
  			`log_attempts` int(11) NOT NULL default '0',
  			`warning_time` int(11) NOT NULL default '0',
  			PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			
		/*
		*	Next, create all of the tables in the database 
		*	Output any mysql errors so JS will know
		*/
		$result1 = mysql_query($query1);
		echo mysql_error();
		$result2 = mysql_query($query2);
		echo mysql_error();
		$result3 = mysql_query($query3);
		echo mysql_error();
		$result4 = mysql_query($query4);
		echo mysql_error();
		$result5 = mysql_query($query5);
		echo mysql_error();
		$result6 = mysql_query($query6);
		echo mysql_error();
		$result7 = mysql_query($query7);
		echo mysql_error();
		$result8 = mysql_query($query8);
		echo mysql_error();
		$result9 = mysql_query($query9);
		echo mysql_error();
		$result10 = mysql_query($query10);
		echo mysql_error();
		$result11 = mysql_query($query11);
		$result12 = mysql_query($query12);
		echo mysql_error();
		$result13 = mysql_query($query13);
		echo mysql_error();
		$result14 = mysql_query($query14);
		echo mysql_error();
		$result15 = mysql_query($query15);
		echo mysql_error();
		$result16 = mysql_query($query16);
		echo mysql_error();
		$result17 = mysql_query($query17);
		echo mysql_error();
		$result18 = mysql_query($query18);
		echo mysql_error();
		$result19 = mysql_query($query19);
		echo mysql_error();
		
		/* Hopefully no errors, output a 1 */
		echo "1";
	}
	if($step=='config')
	{
		$query = "INSERT INTO `config` (`id`, `name`, `value`) VALUES
			(1, 'thread_num_per_page', '10'),
			(2, 'post_num_per_page', '10'),
			(3, 'current_theme', 'default-theme'),
			(4, 'site_title', '{$data['siteTitle']}'),
			(5, 'site_description', '{$data['siteDesc']}'),
			(6, 'admin_email', '{$data['adminEmail']}'),
			(7, 'version', '1.0-alpha'),
			(8, 'registration', 'true'),
			(9, 'email_new_user', 'false'),
			(10, 'current-smilies', 'default-smilies'),
			(11, 'server_time_zone', '-5.0')";
		$query2 = "INSERT INTO `groups` (`id`, `name`, `description`, `username_style`, `username_color`, `perm_can_view_forum`, `perm_can_view_thread`, 				`perm_can_post_thread`, `perm_can_post_reply`, `perm_can_post_links`, `perm_can_edit`, `perm_can_create_poll`, `perm_can_vote`, `perm_can_send_pm`, `perm_can_receive_pm`, `perm_can_receive_alert`, `perm_can_view_profile`, `perm_can_edit_profile`) VALUES
			(1, 'User', 'Generic user', '', '000000', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
			(2, 'Guest', 'Non-logged in user.', '', '000000', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0),
			(3, 'Admin', 'Administrator', '', '000000', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)";
		$result = mysql_query($query);
		echo mysql_error();
		$result2 = mysql_query($query2);
		echo mysql_error();
		
		echo "1";
	}
	if($step=='admin')
	{
		$time_joined = time();
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$password = sha1(secureContent($_POST['adminPass']));
		$query = "INSERT INTO users (username, username_clean, password, email, time_joined, ip_address,group_list,is_admin) VALUES ('{$data['adminName']}','{$data['adminName']}','$password',(SELECT value FROM config WHERE name='admin_email' LIMIT 1),'$time_joined','$ipaddress','1,3','1')";
		$result = mysql_query($query);
		echo mysql_error();
		
		echo "1";
	}
}
?>