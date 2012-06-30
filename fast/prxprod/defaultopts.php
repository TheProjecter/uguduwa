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
 * Set options including all the headers
 */
function setdefaultoptions(&$ch, $server, $sub_req_url){
	$myheaderstogo = array();

	if(isset($server['HTTP_COOKIE']))
		curl_setopt($ch, CURLOPT_COOKIE, $server['HTTP_COOKIE']);
	if(isset($server['HTTP_ACCEPT_CHARSET']))
		array_push($myheaderstogo,"Accept-Charset: ".$server['HTTP_ACCEPT_CHARSET']);
	if(isset($server['HTTP_ACCEPT_LANGUAGE']))
		array_push($myheaderstogo, "Accept-Language: ".$server['HTTP_ACCEPT_LANGUAGE']);
	if(isset($server['HTTP_CONNECTION']))
		array_push($myheaderstogo, "Connection: ".$server['HTTP_CONNECTION']);
	if(isset($server['HTTP_KEEP_ALIVE']))
		array_push($myheaderstogo, "Keep-Alive: ".$server['HTTP_KEEP_ALIVE']);	
	if(isset($server['HTTP_USER_AGENT']))
		curl_setopt($ch, CURLOPT_USERAGENT, $server['HTTP_USER_AGENT']);
	if(isset($server['HTTP_ACCEPT']))	
		array_push($myheaderstogo, "Accept: ".$server['HTTP_ACCEPT']);	
	if(count($myheaderstogo)>0)
		curl_setopt($ch, CURLOPT_HTTPHEADER, $myheaderstogo);

	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	curl_setopt($ch, CURLOPT_TIMEOUT, 180);
	curl_setopt($ch, CURLOPT_AUTOREFERER, FALSE);
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	
	/*binary blaster*/
	curl_setopt($ch, CURLOPT_WRITEFUNCTION, "writefunction");
	
	if(stripos($sub_req_url, "127.0.0.1")===FALSE){
		if(defined("USEPROXY")){
			curl_setopt($ch, CURLOPT_PROXY,USEPROXY);
			if(defined("USEPROXYCREDS")){
				curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, USEPROXYCREDS);
			}
		}	
	}
}
?>