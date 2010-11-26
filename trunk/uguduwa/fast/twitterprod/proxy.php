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
 * THIS IS THE TWITTER PROXY!!!!!!!
 * This customization is to support twitter.
 */


include'../prxprod/host.php';


/*
 * These are defined in the host.php file
 * $myhost
 *
 */
$myproxy		= $_twitter;


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
	$output=preg_replace("/status(\\\\)*\/update/ims", "twitterprod\\1/proxy?urlP=twitter.com\\1/status\\1/update", $output);	
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

/* 
 * THIS IS THE TWITTER PROXY!!!!!!!
 * THIS IS THE TWITTER PROXY!!!!!!!
 */
?>

