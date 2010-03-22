<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/mail.php - functions for sending email
*/

function sendMail($title,$content,$toUser,$toEmail,$fromUser,$fromEmail)
{
	$message = "
		<html>
			<head><title>$title</title></head>
			<body>
				$content
			</body>
		</html>
		";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'To: '.$toUser.' <'.$toEmail.'>' . "\r\n";
		$headers .= "From: $fromUser <$fromEmail>" . "\r\n";
		mail($email1, $title, $message, $headers);
}

function processDomain($domain)
{
	if(strrchr($domain,'www.')!=false)
	{
		$boom = explode("www.",$domain);
		return $boom[1];
	}
	else
	{
		return $domain;
	}
}
?>