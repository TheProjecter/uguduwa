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
	
	Project Uguduwa: uguduwa.wordpress.com
 */	


$security_cookie="uc_xls9861.009";
/*
 * Functions to create a gaurd cookie and then test a guard cookie.
 * This is requires to stop phishing attacks on your proxy.
 */
function getguardcookie($sourceip){
	return safeBase64_encode($sourceip);	
}

function testguardcookie($cookiestring, $sourceip){
	global $security_cookie;

	if(preg_match("/".$security_cookie."=([^;]*)/is", $_SERVER['HTTP_COOKIE'], $matches)){
		$ip=safeBase64_decode($matches[1]);	
		if($ip===$sourceip)
			return 1;
		else
			return 0;	
	}
}


?>