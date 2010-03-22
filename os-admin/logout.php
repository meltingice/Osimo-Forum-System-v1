<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	logout.php - logs a user out of Osimo Admin Panel
*/
	session_start();
	session_destroy();
	header("location: index.php?logout=true");
?>