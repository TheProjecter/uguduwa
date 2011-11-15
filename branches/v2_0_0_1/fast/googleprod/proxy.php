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
 * THIS IS THE GOOGLE PROXY!!!!!!!
 * Runs without any customizations.
 */


include'../prxprod/host.php';


/*
 * These are defined in the host.php file
 * $myhost
  */
$myproxy	= $_google;


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
	
	if(stripos($url, "google.com" )!==FALSE){
		$output=preg_replace("/\"(\/imgres\?imgurl)/ims",  "\"http://www.google.com\\1", $output);
		$output=preg_replace("/<input[^><]*name=\"continue\"[^><]*id=\"continue\"[^><]*\/>/", "", $output);
	}
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