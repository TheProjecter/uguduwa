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

/*
 * Replace quoted urls inside javascript
 */
function grokScriptReplace($matches){
	global $myproxy, $sub_req_url;

	return	grokReplace($matches[0], $matches[2]);
}
function grokScriptSrcReplace($matches){
	global $myproxy,  $sub_req_url;

	return	grokReplace($matches[0], $matches[3]);
}


/*
 * Replace all leftover http* requests.
 */
function grokHttpReplace($matches){
	global $myproxy, $sub_req_url;
	
	return grokReplace($matches[0], $matches[1]);
}


/*
 * Replace src/href/action inside html tags 
 */
function grokSrcReplace($matches){
	global $myproxy, $sub_req_url;

	$tmp;
	$origstring=$matches[0];
	
	if(preg_match("/(src|href|action|code)\s*=\s*([\"'])([^\"'#]*)\\2/ims", $origstring, $matches)){
		if(preg_match("/javascript/is",$matches[3]) ||	stripos($matches[3],$myproxy)===0)
			return $origstring;
		
		if(strcasecmp($matches[1], "action")==0 && 
		!preg_match("/method\s*=\s*([\"'])post\\1/ims",$origstring)){
			$tmp=str_replace($matches[3], $myproxy, $origstring);
			$tmp.="<input type=\"hidden\" name=\"".URLNAMEENC."\" value=\"F".grokReplace("",$matches[3],1)."\"/>";
		}
		else{
			$tmp=grokReplace($origstring, $matches[3]);
		}

	}
	else{
		$tmp=$origstring;
	}
	return $tmp;
}


/*
 * Replace URL() and @import
 */
function grokUrlReplace($matches){
	global $sub_req_url;
	
	return grokReplace($matches[0], $matches[3]);	
}


/*
 * Encode a url, adding hostname or current directory where applicable
 */
function grokReplace($origstring, $match, $retval=0){
 	global $myhost, $myproxy, $loglevel, $regxf, $currentHost, $currentDirectory;

 	if(stripos($match,$myhost)===0)
		return $origstring;

	$oldmatch = $match;	
	$match=str_replace("\\/", "/", $match);
		
	if(preg_match("/^http/i", $match)>0)
    	$url=$match;
	else if(preg_match("/^\//ims", $match)>0)
    	$url="$currentHost$match";
	else
		$url="$currentDirectory/$match";
	
	preg_match("/([^\&]*)(.*)/ims", $url, $meme);
	
	$url = fullyencode($meme[1]).$meme[2];
	
	if(($loglevel & 4) == 4)
		fwrite($regxf, "$url\n");

	if($retval)
		return $url;
	else
  		 return str_replace( $oldmatch, "$myproxy?".URLNAMEENC."=$url", $origstring);
  	
}


/*
 * Grab the headers off the response and seperate the content.
 */
function grokTheHeadersAndRemoveThem(&$output){
	/*Remove this as it usually? does nothing*/
	$output=preg_replace("/(HTTP.*? connection established\\r\\n\\r\\n)/ims", "", $output);
	/* HTTP 100 */
	$output=preg_replace("/(HTTP.*? continue\\r\\n\\r\\n)/ims", "", $output);
	
	if(preg_match("/((HTTP.*?\\r\\n\\r\\n)+)(.*)/ims", $output, $matches)){
		$output=$matches[3];
		return $matches[1];
	}
	else
		return NULL;
}


/*
 * Grab the redirect Location if it exists from the header
 */
function grokLocation($header, $raw=0){
	if(preg_match("/location:\s(.*?)\\r\\n/is", $header, $matches)){
		$loc=$matches[1];
		if(stripos("http", $loc)==0){
			//do nothing	
		}
		else if(stripos("/", $loc)==0){
			$loc="$currentHost$loc";
		}
		else{
			$loc="$currentDirectory/$loc";
		}
		
		if($raw==0)
			return grokReplace($matches[1], $matches[1]);
		else
			return $matches[1];	
	}
	else
		return NULL;		
}


/*
 * Grab the status line from the header 
 */
function grokStatusline($header){
	if(preg_match("/^(http.*?)\\r\\n/is", $header, $matches))
		return $matches[1];
	else
		return NULL;	
}


/*
 * Set the cookies for the return response 
 */
function grokSetCookies($header, $fullcookie=0){
	$count=0;
	if(preg_match_all("/set-cookie:\s(.*?)\\r\\n/is", $header, $matches)){
		foreach($matches[1] as $c){
			setCookieMonster($c, $fullcookie);
			$count++;	
		}
	}
	return $count;		
}


/*
 * Set individual cookies
 */
function setCookieMonster($cookie, $fullcookie=0){
	
	$expire=0;
	$path=NULL;
	$domain=NULL;
	$name=NULL;
	$value=NULL;

	$cookie=$cookie."\n";
	
	if(preg_match("/(expires=(.*?))[;\\n]/im", $cookie, $matches)){
		$expire=strtotime($matches[2]);
		$cookie=str_replace($matches[0], "", $cookie);
	}
	if(preg_match("/(path=(.*?))[;\\n]/im", $cookie, $matches)){
		$path=$matches[2];
		$cookie=str_replace($matches[0], "", $cookie);	
	}
	if(preg_match("/(domain=(.*?))[;\\n]/im", $cookie, $matches)){
		$domain=$matches[2];
		$cookie=str_replace($matches[0], "", $cookie);	
	}
	if(preg_match("/(.*?)=(.*?)[;\\n]/im", $cookie, $matches)){
		$value=$matches[2];
		$name=$matches[1];
	}
	if($fullcookie)
		setrawcookie($name, urlencodecookievalues($value), $expire, $path, $domain);
	else
		setrawcookie($name, urlencodecookievalues($value), $expire);//, $path, $domain);
}


/*
 * Set the current directory and the host
 */
function grokHostAndDir($sub_req_url, &$host, &$dir){
	
	if(preg_match("/(https?:\/\/[^\/\?]*)([^\?]*)/i", $sub_req_url, $matches)>0){
		$host=$matches[1];
		if(isset($matches[2])){
			//if(preg_match("/(.*)\/.+[\.\?].+/i", $matches[2], $matchesX)>0)
			if(preg_match("/(.*)\/[^\/]*/i", $matches[2], $matchesX)>0)
				$dir=$host.$matchesX[1];
			else
				$dir=$host.$matches[2];
		}
		else
			$dir=$matches[1];
		return TRUE;
	}
	else
		return FALSE;
}


/*
 * I using https, then convert all urls to point to https
 */
function makeAllHTTPS($output){
	global $myhost, $_SERVER;
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=="on")
			return str_replace($myhost, str_replace("http", "https", $myhost),  $output);
	else
		return $output;	
}


/*
 * Sometimes setrawcookie fusses because of funny values. Urlencode only a
 * subset of these value.
 */
function urlencodecookievalues($value){
	$bad=array(";",","," ","\t","\r","\n","\013","\014");	
	for($i = 0; $i < count($bad) ; $i++){
		$value=str_replace($bad[$i], urlencode($bad[$i]), $value);
	}
	return $value;
}


/*
 * Change evals into pevals.
 * the peval function will be furnished via encrypter_js.php
 */
function grokChangeEval($output){
	return "peval(";		
}

/*
 * Replace https with http and re-reverse it again
 */
function grokTrueHTTPS($matches){
	return str_replace($matches[1], "http:".$matches[2].$matches[2]."cdpsecurecdp.", $matches[1]);	
}
function grokReverseHTTPS($url){
	return str_replace("http://cdpsecurecdp.", "https://",$url);	
}


/* I don't think we really need this
function  grokFlashvarsReplace($matches){
	global $mybypassproxy;
	$fileurl="$myproxy?".URLNAMEENC."=".grokReplace($matches[0], urldecode($matches[1]),1);
	return str_replace($matches[1], urlencode($fileurl), $matches[0]);
}
*/
?>