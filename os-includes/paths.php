<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/paths.php - sets some useful filepath constants
*	This needs to be included manually before calling the Osimo class
*/

/*Sets Osimo base URL*/
$paths = pathinfo($_SERVER['PHP_SELF']);
$temp = explode('/os-includes',$paths['dirname']);
$temp2 = reset(explode('/os-admin',$temp[0]));
$basepath = "http://".$_SERVER['SERVER_NAME'].$temp2;
if(substr($basepath,-1)!='/'){ $basepath .= "/"; }
define("OSIMOPATH", $basepath);

define("AVATAR_FOLDER",OSIMOPATH."os-content/avatars/");

/* Relative paths */


define("JS_PATH", OSIMOPATH."os-includes/js/");
define("CSS_PATH", OSIMOPATH."os-includes/css/");

/* Absolute paths */
$path = pathinfo(realpath(__FILE__));
$includes = $path['dirname'];
$base = reset(explode('os-includes',$includes));
if(substr($base,-1)!='/'){ $base .= "/"; }

$jspath = $base.'os-includes/js/';
define("ABS_BASEPATH",$base);
define("ABS_JS_PATH",$jspath);
define("ABS_INCLUDES",$includes."/");
define("ABS_ADMIN_INCLUDES",$base.'os-admin/includes/');
define("ABS_AVATAR_FOLDER",$base."os-content/avatars/");

/* Set paths to the theme folder */
include_once(ABS_INCLUDES.'theme.php');
$theme = _getCurrentTheme(false);
$themepath = $base."os-content/themes/$theme/";
define("THEMEPATH", "os-content/themes/$theme/");
define("THEMEPATH_URL",OSIMOPATH."os-content/$theme/");
define("ABS_THEMEPATH",$themepath);
define("ABS_THEME_FOLDER",$base."os-content/themes/");
?>