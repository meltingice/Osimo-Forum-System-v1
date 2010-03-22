<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/security.php - functions for security in Osimo
*/

/* Escape strings/arrays for insertion to MySQL */
function secureContent($content)
{
	if(is_array($content))
	{
	    foreach($content as $key=>$val)
	    {
	    	$content[$key] = mysql_real_escape_string(html_entity_decode($val));
	    }
	    
	    return $content;
	}
	else
	{
	    return mysql_real_escape_string(html_entity_decode($content));
	}
}
?>