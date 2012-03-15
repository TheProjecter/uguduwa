<?php
/*
	Copyright 2010 Ping Shin Ching (ping_sc@hotmail.com)

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

		http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
	
	Project Uguduwa: http://www.tidytutorials.com/p/uguduwa.html
 */	


/* 
 * THIS IS THE FACEBOOK PROXY!!!!!!!
 * This customization is to support facebook.
 */


include'../prxprod/host.php';


/*
 * These are defined in the host.php file
 * $myhost
 */
$myproxy	= $_facebook;


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
	global $myurlnameEnc, $myproxy;
	$output=preg_replace("/href=((\\\\)?)\"[^\"]*\\1\"/ims"," \\0 target=_top ", $output);

	$output=preg_replace("/api\.recaptcha\.net/ims", str_replace("http://","", $myproxy)."?$myurlnameEnc=".fullyencode("http://api.recaptcha.net"), $output);
 	$output=preg_replace("/api\.secure-recaptcha\.net/ims", str_replace("http://","", $myproxy)."?$myurlnameEnc=".fullyencode("http://api-secure.recaptcha.net"), $output);
 	
	//$output=preg_replace("/api\.recaptcha\.net/ims", "127.0.0.1/php/fast/facebookprod/proxy?urlE=nszpo4ZgnZfI3mG.ltGXyOmWtJKcpL3p198701", $output);
 	//$output=preg_replace("/api\.secure-recaptcha\.net/ims", "127.0.0.1/php/fast/facebookprod/proxy?urlE=nszpo4ZgnZfI3mC-ltGrytphvpbRl8jplrSSnKS96Q__198701", $output);


 	/***In development***/
 	//$output=preg_replace("/((\\\\*)\/ajax)/ims", "http:\\2/\\2/www.facebook.com\\2/ajax", $output);
 	
 	//$output=preg_replace("/action=((\\\\)?)\"[^\"]*\\1\"/ims"," \\0 target=_top ", $output);
 	
 	//$output=preg_replace("/(\\\\\/\\\\\/www.facebook.com\\\\\/common\\\\\/blank.html)/ims", "\\/\\/127.0.0.1\\/php\\/fast\\/prxprod\\/proxy?urlP=http:\\/\\/www.facebook.com\\/common\\/blank.html", $output);

 	return true;
}


/*I*
 * Implementation of the special set curl options
 *I*/
function myspecialcurloptionsI(&$ch, $sub_req_url){
	return true;
}


/* Call the proxy core */
include'../prxprod/defaultproxy.php';

?>
