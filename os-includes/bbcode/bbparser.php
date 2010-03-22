<?php
/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/bbcode/bbparser.php - osimo's bbcode parser
*	This can be used on any site, just call bb2html()
*/
function bb2html($post)
{	
	/*******************************************
	* PLEASE READ: If you are not using this   *
	* BBCode parser as a part of Osimo, change *
	* $isOsimo below to false or else you will *
	* get errors!                              *
	*******************************************/
	$isOsimo = true;
	
	/* Remove all HTML tags from the string just in case any got through somehow */
	$post = strip_tags($post);
	
	/* Remove any attempts to run javascript */
	$post = str_ireplace('javascript:','',$post);
	
	$post = runNoBBScan($post);
	
	/* Define some basic BBCode */
	$bbcode = array(
		"[b]","[/b]",
		"[i]","[/i]",
		"[u]","[/u]",
		"[list]","[/list]",
		"[*]","[/*]",
		"[code]","[/code]",
		"[right]","[/right]",
		"[left]","[/left]",
		"[center]","[/center]",
		"[row]","[/row]",
		"[img]","[/img]");
	
	/* ... and its HTML equivalent */
	$html = array(
		"<strong>","</strong>",
		"<i>","</i>",
		"<u>","</u>",
		"<ul>","</ul>",
		"<li>","</li>",
		"<code>","</code>",
		"<div style=\"text-align:right\">","</div>",
		"<div style=\"text-align:left\">","</div>",
		"<div style=\"text-align:center\">","</div>",
		"<tr>","</tr>",
		"<img src=\"","\" />");
	
	$post = bbCleaner($post);
	
	/* Convert all the basic BBCodes */
	$post = str_ireplace($bbcode,$html,$post);
	
	/* Now for the more complicated BBCode conversion */
	$post = runBBScan($post);
	
	$post = str_replace("\n","<br />",$post);
	
	/* Finally, process smilies */
	if($isOsimo){ $post = processSmilies($post); }
	
	return $post;
}

/* No-BBCode Scan
*  Scans the post and makes sure that
*  everything in-between [nocode][/nocode]
*  does not get interpreted as bbcode
*/
function runNoBBScan($post,$offset=0)
{
	$type = "nocode";
	$tag = "[$type]";
	$pos = stripos($post,$tag,$offset);
	if($pos!==false)
	{
		$offset = $pos+strlen($tag);
	    $pos2 = stripos($post,"[/$type]",$offset);
	    if($pos2!==false)
	    {
	    	$data = substr($post,$offset,($pos2-$offset));
	    	$offset = $pos2 + strlen("[/$type]");
	    }
	    
	    $edited = str_replace("[","&#91;",$data);
	    $edited = str_replace("]","&#93;",$edited);
	    $post = substr_replace($post,$edited,$pos,(($pos2+strlen("[/$type]"))-$pos));
	    
	    return runNoBBScan($post,$offset);
	}
	
	return $post;
}

/*
*  BBCode cleaner
*  Checks to make sure all tags are closed.
*  Not too fancy, but avoids html errors
*/
function bbCleaner($post)
{
	$bbcode = array(
		"b",
		"i",
		"u",
		"list",
		"*",
		"code",
		"right",
		"left",
		"center"
	);
	
	foreach($bbcode as $tag)
	{
		/* Replace all tags with lower-case versions */
		$post = str_ireplace("[$tag]","[$tag]",$post);
		$post = str_ireplace("[/$tag]","[/$tag]",$post);
		
		/* Count number of open tags */
		$open = substr_count($post,strtolower("[$tag]"));
		//$open += substr_count($post,strtoupper("[$tag]"));
		
		/* Count number of closed tags */
		$closed = substr_count($post,strtolower("[/$tag]"));
		//$closed += substr_count($post,strtoupper("[/$tag]"));
		
		/* Case 1: More open than closed */
		if($open>$closed)
		{
			for($i=0;$i<($open-$closed);$i++)
			{
				$post .= "[/$tag]";
			}
		}
		/* Case 2: More closed than open */
		elseif($closed>$open)
		{
			for($i=0;$i<($closed-$open);$i++)
			{
				$post = "[$tag]".$post;
			}
		}
	}
	
	return $post;
}

function bbChecker($post){
	$simBBCodeOpen = array(
		"[b]","[i]",
		"[u]","[list]",
		"[code]",
		"[right]","[left]",
		"[center]","[row]",
		"[img]","[spoiler]",
		"[size=","[font=",
		"[color=","[align=",
		"[nocode]");
	$fancyBBCodeOpen = array(
		"email","url",
		"quote","cell",
		"table"
	);

	$errors = array();
	for($i=0;$i<count($simBBCodeOpen);$i++){
		$bbcodeClose = str_replace("=","]",substr_replace($simBBCodeOpen[$i],"/",1,0));
		$numOpen = substr_count($post,$simBBCodeOpen[$i]);
		$numClose = substr_count($post,$bbcodeClose);
		if($numOpen > $numClose){
			$errors[] = "More open {$simBBCodeOpen[$i]} tags than closed";
		}
		if($numOpen < $numClose){
			$errors[] = "More close $bbcodeClose tags than open";
		}
	}
	
	for($i=0;$i<count($fancyBBCodeOpen);$i++){
		$check1 = "[{$fancyBBCodeOpen[$i]}]";
		$check2 = "[{$fancyBBCodeOpen[$i]}=";
		$bbcodeClose = "[/{$fancyBBCodeOpen[$i]}]";
		
		$numOpen = substr_count($post,$check1) + substr_count($post,$check2);
		$numClose = substr_count($post,$bbcodeClose);
		if($numOpen > $numClose){
			$errors[] = "More open $check1 or $check2 tags than closed";
		}
		if($numOpen < $numClose){
			$errors[] = "More close $bbcodeClose tags than open";
		}
	}
	
	if(count($errors) > 0){
		$errmsg = "The bbcode entered contains the following errors:\n";
		foreach($errors as $error){
			$errmsg .= "- $error.\n";
		}
		
		return $errmsg;
	}
	else{
		return true;
	}
}

function runBBScan($post)
{
	$post = fancyBB($post,'email');
	$post = fancyBB($post,'url');
	$post = fancyBB($post,'size');
	$post = fancyBB($post,'font');
	$post = fancyBB($post,'color');
	//$post = fancyBB($post,'img');
	$post = fancyBB($post,'spoiler');
	$post = fancyBB($post,'quote');
	$post = fancyBB($post,'align');
	$post = fancyBB($post,'cell');
	$post = fancyBB($post,'table');
	
	return $post;
}

function fancyBB($post,$type,$offset=0)
{
	$tag = "[$type]";

	$pos = stripos($post,$tag,$offset);
	if($pos!==false)
	{
	    $offset = $pos+strlen($tag);
	    $pos2 = stripos($post,"[/$type]",$offset);
	    if($pos2!==false)
	    {
	    	$data = substr($post,$offset,($pos2-$offset));
	    	$offset = $pos2 + strlen("[/$type]");
	    }
	}
	else
	{
		/* check for tag with attribute */
		$_tag = "[$type=";
		$_pos = stripos($post,$_tag,$offset);
		if($_pos!==false)
		{
			/* Attr found, get value */
			$offset = $_pos+strlen($_tag);
			$_pos2 = strpos($post,']',$offset);
			if($_pos2!==false)
			{
				/* Got attr value */
				$data1 = substr($post,$offset,($_pos2-$offset));
				$offset = $_pos2+1;
				$_pos3 = stripos($post,"[/$type]",$offset);

				if($_pos3!==false)
				{
					$data2 = substr($post,$offset,($_pos3-$offset));
					$offset = $_pos3 + strlen("[/$type]");
					$attr = true;
				}
			}
		}
		else
		{
			return $post;
		}
	}
	
	if($type=='email')
	{
		if($attr)
		{
			$search = "[email=".$data1."]".$data2."[/email]";
			if(strpos($data1,"\"")!==false){ $data1 = str_replace("\"","",$data1); }
			$replace = "<a href=\"mailto:$data1\">$data2</a>";
		}
		else
		{
			$search = "[email]".$data."[/email]";
			$replace = "<a href=\"mailto:$data\">$data</a>";
		}
		
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='url')
	{
		if($attr)
		{
			$search = "[url=".$data1."]".$data2."[/url]";
			if(strpos($data1,"\"")!==false){ $data1 = str_replace("\"","",$data1); }
			if(strpos($data1,"mailto:")===false)
			{
				$data1 = urlFilter($data1);
			}
			
			$data2 = runBBScan($data2);
			
			$replace = "<a target=\"_blank\" href=\"$data1\">$data2</a>";
		}
		else
		{
			$search = "[url]".$data."[/url]";
			$data = urlFilter($data);
			$replace = "<a target=\"_blank\" href=\"$data\">$data</a>";
		}
		
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='img')
	{
		$search = "[img]".$data."[/img]";
		$data = urlFilter($data);
		$replace = "<img src=\"$data\" alt=\"image\" />";
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='size')
	{
		$search = "[size=".$data1."]".$data2."[/size]";
		$data2 = runBBScan($data2);
		if(strpos($data1,"\"")!==false){ $data1 = str_replace("\"","",$data1); }
		
		if(strpos($data1,"px")!==false){ $replace = "<span style=\"font-size:{$data1}\">{$data2}</span>"; } 
		else { $replace = "<span style=\"font-size:{$data1}px\">{$data2}</span>"; }
		
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='font')
	{
		$search = "[font=".$data1."]".$data2."[/font]";
		$data2 = runBBScan($data2);
		if(strpos($data1,"\"")!==false){ $data1 = str_replace("\"","",$data1); }
		$replace = "<span style=\"font-family:{$data1}\">{$data2}</span>";
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='color')
	{
		$search = "[color=".$data1."]".$data2."[/color]";
		$data2 = runBBScan($data2);
		if(strpos($data1,"\"")!==false){ $data1 = str_replace("\"","",$data1); }
		$replace = "<span style=\"color:{$data1}\">{$data2}</span>";
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='spoiler')
	{
		$search = "[spoiler]".$data."[/spoiler]";
		$data = runBBScan($data);
		$replace = "<span style=\"background: #000000 none repeat scroll 0% 0%; color: #000000; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial\">{$data}</span>";
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='quote')
	{
		if($attr)
		{
			$search = "[quote=".$data1."]".$data2."[/quote]";
			$data2 = runBBScan($data2);
			$replace = "<blockquote><span class=\"blockquote-title\">Quote: original post by {$data1}</span><br />".trim($data2)."</blockquote>";
			$post = str_ireplace($search,$replace,$post);
		}
		else
		{
			$search = "[quote]".$data."[/quote]";
			$data = runBBScan($data);
			$replace = "<blockquote><span class=\"blockquote-title\">Quote:</span><br />".trim($data)."</blockquote>";
			$post = str_ireplace($search,$replace,$post);
		}
	}
	if($type=='align')
	{
		$search = "[align=".$data1."]".$data2."[/align]";
		$data2 = runBBScan($data2);
		$replace = "<div style=\"text-align:".$data1."\">".$data2."</div>";
		$post = str_ireplace($search,$replace,$post);
	}
	if($type=='table')
	{
		if($attr){
			$search = "[table=".$data1."]".$data2."[/table]";
			$data2 = str_replace("\n","",$data2);
			$data2 = runBBScan($data2);
			$table_attr = parseTableAttr($data1);
			$replace = "<table $table_attr>".$data2."</table>";
			$post = str_ireplace($search,$replace,$post);
		}
		else{
			$search = "[table]".$data."[/table]";
			$data = str_replace("\n","",$data);
			$data = runBBScan($data);
			$replace = "<table>".$data."</table>";
			$post = str_ireplace($search,$replace,$post);
		}
	}
	if($type=='cell')
	{
		if($attr){
			$search = "[cell=".$data1."]".$data2."[/cell]";
			$data2 = runBBScan($data2);
			$cell_attr = parseTableCellAttr($data1);
			$data2 = str_replace("\n","<br />",$data2);
			$replace = "<td $cell_attr>".$data2."</td>";
			$post = str_ireplace($search,$replace,$post);
		}
		else{
			$search = "[cell]".$data."[/cell]";
			$data = runBBScan($data);
			$data = str_replace("\n","<br />",$data);
			$replace = "<td>".$data."</td>";
			$post = str_ireplace($search,$replace,$post);
		}
	}
	
	if($offset<strlen($post))
	{
		return fancyBB($post,$type,$offset);
	}
	
	return $post;
}

function parseTableAttr($data){
	$html = array("cellspacing","cellpadding");
	$styles = array("width","height","border","border-width","border-color","background-color","color");
	$attr = array();
	$attr = explode(";",$data);
	if(count($attr)==0){
		$attr[0] = $data;
	}
	
	$style = "";
	$html_attr = "";
	for($i=0;$i<count($attr);$i++){
		$temp = explode(":",$attr[$i]);
		if(in_array(strtolower($temp[0]),$styles)){
		    $style .= $attr[$i].";";
		}
		elseif(in_array(strtolower($temp[0]),$html)){
			$html_attr .= $temp[0]."=\"".$temp[1]."\" ";
		}
		elseif(strtolower($temp[0]) == 'cell-border'){
			$html_attr .= "border=\"".$temp[1]."\" ";
		}
		else{ /* continue */ }
	}
	
	$content = $html_attr." style=\"".$style."\"";
	
	return $content;
}

function parseTableCellAttr($data){
	$styles = array("width","height","background-color","color","border");
	$attr = array();
	$attr = explode(";",$data);
	if(count($attr)==0){
		$attr[0] = $data;
	}
	
	$style = "";
	for($i=0;$i<count($attr);$i++){
		$temp = explode(":",$attr[$i]);
		
		if(in_array(strtolower($temp[0]),$styles)){
			$style .= $attr[$i].';';
		}
	}
	
	$content = "style=\"".$style."\"";
	
	return $content;
}

function urlFilter($url)
{
	$allowedProtocols = array("http","ftp","steam");
	
	$pos = strpos($url,':/');
	$protocol = substr($url,0,$pos);
	
	if($pos===false)
	{
		/* Assume http protocol */
		return "http://".$url;
	}
	else
	{
		if(in_array($protocol,$allowedProtocols))
		{
			/* URL is already set and ready */
			return $url;
		}
		else
		{
			return substr($url,$pos+3);
			
		}
	}
	
}

function processSmilies($post)
{
	if(!isset($_SESSION['osimo']['options']['smileySet']))
	{
		$query = "SELECT value FROM config WHERE name='current-smilies' LIMIT 1";
		$result = mysql_query($query);
		if($result)
		{
			$_SESSION['osimo']['options']['smileySet'] = reset(mysql_fetch_row($result));
		}
	}
	
	/* If smiley BBCode isn't cached, then do so */
	if(!is_array($_SESSION['osimo']['smilies']))
	{
		$query = "SELECT code,image FROM smilies WHERE smileySet='".$_SESSION['osimo']['options']['smileySet']."'";
		$result = mysql_query($query);
		
		if($result)
		{
			$i=0;
			while(list($code,$image)=mysql_fetch_row($result))
			{
				$_SESSION['osimo']['smilies']['name'][$i] = $image;
				$_SESSION['osimo']['smilies']['code'][$i] = $code;
				$i++;
			}
		}
	}
	
	if(is_array($_SESSION['osimo']['smilies']))
	{
		$j=0;
		foreach($_SESSION['osimo']['smilies']['name'] as $smiley)
		{
			$replace[$j] = "<img src=\"".OSIMOPATH."os-content/smilies/".$_SESSION['osimo']['options']['smileySet']."/$smiley\" />";
			$j++;
		}
		
		$post = str_replace($_SESSION['osimo']['smilies']['code'],$replace,$post);
	}
	
	return $post;
}
?>