<?php
/*
	Copyright (C) 2010  © 2010 Ping Shin Ching. All rights reserved. 
	Unauthorized use prohibited.

	Ping Shin Ching's copyright materials - which include source code and
	any configuration files - may not be reproduced in whole or in part by 
	persons, organizations or corporations without the prior written 
	permission of the sole owner Ping Shin Ching.

	Ping Shin Ching
	righteous.ninja@gmail.com OR ping_sc@hotmail.com
 */


/* 
 * THIS IS THE YOUTUBE PROXY!!!!!!!
 * This customization is to support youtube.
 */


include'../prxprod/host.php';


/*
 * These are defined in the host.php file
 * $myhost
 */
$myproxy		= $_youtube;
$bypassproxy 	= $_youtube; //bypass proxy has stopped working for some reason so use youtube

/*
 * 0 nothing
 * 5 log theregxs
 * 6 log incomming data
 */
$loglevel=0;


/*I*
 * Implementation of special grok function.
 * This will get called before all the major grokkers
 *I*/
function myspecialgrokkerI(&$output, $url){
	global $bypassproxy, $myurlnameEnc, $_SERVER;
	
	$BIGNUMBER=10000000;
	$fileurl="";
	
	if( preg_match("/www\.youtube\.com\/watch\?v=/ims",$url) &&
		($fileurl=getYoutubeUrl($output))!==NULL){

		//error_log("FURL: $fileurl");	
			
		$fileurl="$bypassproxy?$myurlnameEnc=".grokReplace($fileurl, $fileurl,1);

		if(($start=stripos($output,"<div id=\"watch-player-div\"",0))!==FALSE ||
			($start=stripos($output,"<div id=\"watch-player\"",0))!==FALSE){
				
$embed=<<<EMBED
		<div id="watch-player-divx" class="flash-player"><embed 
		src="{player}"
        width="620"
        height="380"
        bgcolor="000000"
        allowscriptaccess="always"
        allowfullscreen="true"
        type="application/x-shockwave-flash"
        pluginspage="http://www.macromedia.com/go/getflashplayer" 	
EMBED;

			$embed = preg_replace("/\{player\}/ims", 
				preg_replace("/(.*\/).*/ims", "\\1player-viral.swf", $bypassproxy), $embed);
			
			$fileurl=urlencode($fileurl);
			$embed.="\nflashvars=\"width=620&amp;height=380&amp;type=video&amp;fullscreen=true&amp;volume=100&amp;autostart=true&amp;file=$fileurl\" /></div>";
		
			$more=1;
			$searchp=$start;
			while($more>0){
				if(($dcl=stripos($output,"</div>", $searchp+1))===FALSE){
					$dcl=$BIGNUMBER;
				}
				if(($dop=stripos($output,"<div", $searchp+1))===FALSE){
					$dop=$BIGNUMBER;
				}
				if($dop<$dcl){
					$searchp=$dop;	
					$more++;
				}else{
					$searchp=$dcl;
					$more--;
				}
			}
			
		     $end=$searchp+6;
			//$end=stripos($output,"</div>", stripos($output,"</div>", $start)+6);
			
			$x=substr($output, 0, $start);
			$x.=$embed;
			$x.=substr($output, $end);
			$output=$x;
		}
	}
	return true;
	
}


/*I*
 * Implementation of the special set curl options
 *I*/
function myspecialcurloptionsI(&$ch, $sub_req_url){
	return true;
}


/*
 * This is used to extract the Youtube URL from the site.
 */
function getYoutubeUrl($output){
	if(preg_match("/fmt_stream_map\": \"\d*\|([^\",]*)/ims", $output, $matches)){
		return urldecode($matches[1]);
	}
	else{
		return NULL;
	}
}


/*
function getYoutubeUrl($url, $_SERVER){
	$yt=curl_init();
	
	curl_setopt($yt, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($yt, CURLOPT_URL, "http://kej.tw/flvretriever/");
	curl_setopt($yt, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
	
	$encoded = urlencode("videoUrl").'='.urlencode($url);

	curl_setopt($yt, CURLOPT_POST, 1);
	curl_setopt($yt, CURLOPT_POSTFIELDS,  $encoded);

	$output=curl_exec($yt);

	if(preg_match("/<a\s*href=\"([^\"]*)\"\s*id=\"vurl\"\s*>/ims", $output, $matches))
		return $matches[1];
	else
		return NULL;
}
*/


/* Call the proxy core */
include'../prxprod/defaultproxy.php';
?>

