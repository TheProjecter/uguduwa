<?php 
/*
    Code Diaries Proxy Core - Web Proxy
    Copyright (C) 2010  Ping Shin Ching

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/licenses.

	Ping Shin Ching
	righteous.ninja@gmail.com OR ping_sc@hotmail.com
	More information at http://codediaries.com
 */


$postrequest;
$myheaderstogo = array();

include 'defaultopts.php';
include 'grokkers.php';
include 'coders.php';
include 'regxs.php';
include 'adds.php';
include 'contentfilter.php';
include 'blockedips.php';

set_time_limit(120);

$superproxy="http://127.0.0.1/php";


$httpStatus;
$sub_req_url="";
$output="";
$rating="";
$contentType="";
$headers="";

/* These are for the writefunction called via CURLOPT_WRITEFUNCTION */
$finishedheaders=0;
$redirectincallback=0;
$binarydata=0;


/*
 * This is for the index page.
 */
if(strcasecmp($_SERVER['REQUEST_URI'], "/")==0){
	$f = fopen("index.html", 'r');
	$d= fread($f, filesize("index.html"));
	fclose($f);
	echo $d;
	exit(0);
}

/*
 * Reverse any of the re-written https urls.
 */
$sub_req_url = grokReverseHTTPS($_SERVER['REQUEST_URI']);
/*
 * Check to see if the requested site falls within the walled garden 
 */
$inthegarden=0;
foreach($walledgarden as $gardenpatch){
	if(stripos($sub_req_url, $gardenpatch)!==FALSE){
		$inthegarden=1;
		break;	
	}
}



/*
 * Check to see if the the requesting IP and or the site requested is banned
 */
if(!$inthegarden){
foreach($blocked_ips as $blockedip => $urlmatch){
	if ( ($_SERVER['REMOTE_ADDR']==$blockedip && ($urlmatch==""||stripos($_SERVER['REQUEST_URI'],$urlmatch)!==FALSE))
		||
	 (stripos($_SERVER['REQUEST_URI'],$urlmatch)!==FALSE && ($blockedip==""||$_SERVER['REMOTE_ADDR']==$blockedip )) 
	 ){
		error_log("blocked [$blockedip] $urlmatch");
		header("Location: http://truecodeproxy.com/indexnopig.html");
		exit(0);
	}
}
}



/*
 * Initialize curl !!!!!!!
 */
$ch = curl_init();


/*
 * Set the default curl options and copy any headers to the outgoing request 
 */
setdefaultoptions($ch, $_SERVER, $sub_req_url);
curl_setopt($ch, CURLOPT_REFERER, $sub_req_url );
curl_setopt($ch, CURLOPT_URL, $sub_req_url);


/*
 * Some extra care if the method is a POST request
 */
$encoded="";
if(strstr($_SERVER['REQUEST_METHOD'], "POST")){
	foreach($_POST as $name => $value) {
		//$name = $name;
  		$encoded .= urlencode($name).'='.urlencode($value).'&';
	}
	$encoded = substr($encoded, 0, strlen($encoded)-1);
	
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
}


if(($x=curl_exec($ch))===FALSE){
	curl_close($ch);
	exit(0);
}


/*
 * the writefunction callback for CURLOPT_WRITEFUNCTION
 */
function writefunction($ch, $data){
	global $output,	
	$contentType, $sub_req_url, $httpStatus, $finishedheaders,
	$redirectincallback, $headers, $binarydata;

	if($binarydata){
 		echo $data;
 	}
 	else{
		$output .= $data;
		if(!$finishedheaders && ($headers=grokTheHeadersAndRemoveThem($output))!=NULL){
			$finishedheaders=1;
			$contentType 	= curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			$sub_req_url	= curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			$httpStatus 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);

			/* Set the statusline and headers and cookies for the reply */
 			header(grokStatusline($headers));
		
			grokSetCookies($headers, 1);

			if(isset($contentType) && $contentType!=""){
				header("Content-type: $contentType");
				if(stripos($contentType, "text")===FALSE &&
 					stripos($contentType, "javascript")===FALSE){
					$binarydata=1;
					echo $output;
 				}
			}
			else{
				$binarydata=0;
			}
				
			if($httpStatus>=300 && $httpStatus<400){
				$redirectincallback=1;
			}

 		}
 	}

	return strlen($data);	
}


/*
 * If there was a redirrect in thewritefunction handle it here.
 */
if($redirectincallback){
	$loc = grokLocation($headers, 1);
	$loc = preg_replace_callback(TRUEHTTPS_REGX, "grokTrueHTTPS", $loc);
	header("Location: $loc");
	exit(0);
}


/*
 * Close the curl connection
 */
curl_close($ch);


if($binarydata){
	//do nothing at the moment
}
else{
	ob_start("ob_gzhandler");
	/*
	 *  Replace any URLs and add the banners 
	 */
	if((stripos($contentType, "text")!==FALSE ||
		stripos($contentType, "javascript")!==FALSE) &&
		!$inthegarden &&
		($httpStatus>=200 && $httpStatus<300) ){
		
	 	/* Change any https links to go via http. */
		$output = preg_replace_callback(TRUEHTTPS_REGX, "grokTrueHTTPS", $output);
		
		if(preg_match("/\b(html)\b/ims", $contentType))
			$output=getSuperHeadmaster($output, $rating);
	}
	/*
 	* Send the output back to the client !!!!!!!!!!!!!!!!!!!!!
 	*/
	echo $output;
	/*
	 * Flush all the output
	 */
	ob_end_flush();
}



//error_log("Get: $rating $sub_req_url");	


/*
 * Function to create the banner to display on the proxified pages.
 */
function getSuperHeadmaster($output, &$rating){
	global $superproxy, $mystylesheet, $sub_req_url;
	
	$myaddcounter	= "http://173.201.183.172/fast/prxprod/addcounter.php";
	$divheight	= 90;
	$hideheight	= 27;
	$hidewidth = 50;
	$totaldivheight=($divheight+40);
	$external=0;
	$bgcolor="";

	
	$rating=getContentRating($output);
	
	$adpage=getMyAdd($rating);
	
	if($rating=="G")
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:green\\\">&nbsp;G&nbsp;</font>";
	else if($rating=="M")
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:blue\\\">&nbsp;M&nbsp;</font>";	
	else if($rating=="R")
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:red\\\">&nbsp;R&nbsp;</font>";	
	else
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:#cc9900\\\">&nbsp;U&nbsp;</font>";

		
	$head  =	"<div id=\"add_place_dvx\" style=\"height:".$totaldivheight."px;overflow:hidden\"></div><div scrolling=\"no\" style=\"margin:0 0 0 0;z-index:1000;overflow:hidden;position:absolute;top:0px;left:0px;width:101%;\" id=\"add_link_dvx\"></div>".
				"<script>".
				"if(top.location==self.location){".
/*cnt*/ 		"document.write('<link rel=\\\"stylesheet\\\" type=\\\"text/css\\\" href=\\\"$myaddcounter?p=".urlencode($sub_req_url)."&r=$rating\\\"></link>');".
				"var x=document.getElementById('add_link_dvx');x.style.height=\"".$totaldivheight."px\";x.visibility=\"visible\";".
				"x.innerHTML=\"<table class=\\\"prxtbl\\\" style=\\\"border-width:0;border-collapse:collapse;width:99%;".($bgcolor==""?"":"background-color:#$bgcolor")."\\\">".
				"<tr><td><table class=\\\"prxtbl\\\" style=\\\"".($bgcolor==""?"":"background-color:#$bgcolor")."\\\"><tr><td><span class=\\\"prxflnk\\\" id=\\\"add_show_dvx\\\"></span></td><td>&nbsp</td><td>&nbsp;&nbsp;</td><td><span class=\\\"prxftxt\\\"> You are using the code diaries TruProxy</span></td><td>&nbsp;</td><td>$ratingicon</td><td style=\\\"width:100%\\\">&nbsp;</td><td><span class=\\\"prxflnk\\\" id=\\\"add_hide_dvx\\\"><a class=\\\"prxflnk\\\" href=\\\"javascript:hidemeplease()\\\">Hide!</a></span></td><td><img style=\\\"text-align:right;width:30px;border:0\\\" src=\\\"http://173.201.183.172/fast/prxprod/help/ico_prxycore.gif\\\"/></td></tr></table></td></tr>".
				"<tr><td><iframe id=\\\"add_link_ifrmx\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" class=\\\"prxifrm\\\" src=\\\"$adpage\\\"></iframe></td></tr></table>\";".
				"document.getElementById('add_link_ifrmx').style.height=\"".$divheight."px\";".
				"}else{".
				"document.getElementById('add_place_dvx').style.height=\"0px\";}".
				"function hidemeplease(){var x=document.getElementById('add_link_dvx');x.style.height=\"".$hideheight."px\";x.style.width=\"".$hidewidth."px\";var y=document.getElementById('add_place_dvx');y.style.height=\"".$hideheight."px\";y.style.width=\"".$hidewidth."px\"; document.getElementById('add_hide_dvx').innerHTML=\"\";document.getElementById('add_show_dvx').innerHTML=\"<left><a class=\\\"PRXformAHide\\\" href=\\\"javascript:showmeplease()\\\">Show!</a>&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/></left>\";  }".
				"function showmeplease(){var x=document.getElementById('add_link_dvx');x.style.height=\"".$totaldivheight."px\";x.style.width=\"100%\";var y=document.getElementById('add_place_dvx');y.style.height=\"".$totaldivheight."px\";y.style.width=\"100%\";document.getElementById('add_show_dvx').innerHTML=\"\";document.getElementById('add_hide_dvx').innerHTML=\"<a class=\\\"PRXformAHide\\\" href=\\\"javascript:hidemeplease()\\\">Hide!</a>\";}".
				"</script>";
	
	$output=preg_replace("/<\s*\/\s*head\s*>/ims", "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$mystylesheet."\" /></head>", $output);
	

	if(preg_match(SRC_REGX_BODYTAG, $output, $matches))
		return str_replace($matches[0], $matches[0].$head, $output);
	else
		return $output;
	
}





?>
