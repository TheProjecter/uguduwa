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
 * The main constants used in coders.php
 */
$myurldelimiter="198701";
$myinfodelimiter="||";
$mycodeword="Xu3L1n6";


/*
 * Functions to safely encode and decode URLs
 */
$safeBase64chars = array("/"=>'-', "="=>'_', "+"=>'.');    
function safeBase64_encode($str){
  global  $safeBase64chars, $mycodeword;

  $str = xencrypt($str, $mycodeword);
  $str = base64_encode($str);
  foreach($safeBase64chars as $from=>$to){
    $str = str_replace($from, $to, $str); 
  }
  return $str;
}
function safeBase64_decode($str){
  global  $safeBase64chars, $mycodeword;
  foreach($safeBase64chars as $from=>$to){
    $str = str_replace($to, $from, $str); 
  }
  //return base64_decode($str);
  return xdecrypt(base64_decode($str), $mycodeword);
}


/*
 * Fully encode an URL including the delimiter
 */
function fullyencode($url){
	global $dataarray, $myurlnameEnc, $myurldelimiter, $myinfodelimiter;
	return safeBase64_encode($url.rawencode($dataarray)).$myurldelimiter;
	
}


/*
 * Raw encode and raw decode URLs with the additional information
 */
function rawencode($data){
	global $myinfodelimiter;
	$tmp="";
	foreach( $data as $keys=>$values)
		$tmp.="$myinfodelimiter$keys=$values";	
	return $tmp;	
}
function rawdecode($dataarray, $datastring){
	global $myinfodelimiter;
	$tmpa = explode($myinfodelimiter, $datastring);
	foreach($tmpa as $value){
 		preg_match("/(.*)=(.*)/ims", $value, $matches);
 		$dataarray["$matches[1]"]="$matches[2]";
 	}
	return $dataarray;
}


/*
 *  Replace any funny characters
 */
function replaceFunnyChars($url){
	$jscriptchars= array("\\x3a"=>':',"\\x2f"=>'/',"\\x3f"=>'?',"\\x3d"=>'=',"\\x26"=>'&');	
	
	$url = htmlspecialchars_decode($url);
	foreach( $jscriptchars as $jschars=>$chars){
		$url= str_replace($jschars, $chars, $url);	
	}
	$url = str_replace("\\\/","/", $url);
	$url = str_replace("\/","/", $url);
	return $url;
}


/*
 * Decode the GET url - Calls itself recursively untill all the URLs are decoded
 */
function buildGetRequest($uri){
	global $myurlnameEnc,  $myproxy, $myurldelimiter, $myinfodelimiter, $dataarray;
	
	$uri=urldecode($uri);
	
	$xp = str_replace("/", "\/", $myproxy);
	if(preg_match("/(.*?)(($xp\?)?)$myurlnameEnc=(F?)(.*?)$myurldelimiter(.*)/ms", $uri, $matches)){
		
		$body=safeBase64_decode($matches[5]);
		$url="";
		
		if(($p1=strpos($body, $myinfodelimiter))>1){
			$url = substr($body, 0, $p1);
			$body = substr($body, $p1+strlen($myinfodelimiter));
			$dataarray=rawdecode($dataarray, $body);
		}
		else
			$url=$body;
		
			
		if($matches[4]=="F"){
			$url=$matchs[1].$url."?".$matches[6];
		}
		else{
			$url=$matches[1].$url.$matches[6];
		}
		
		//echo "e:$url\n";
		return buildGetRequest($url);
	}
	else{
		//echo "exiting\n $url";
		//exit(0);
		return replaceFunnyChars($uri);	
	}
	
}


/*
 * Encode base tag and pre-pend proxy to it. 
 */
function encodeBaseTag($host){
	global $myproxy, $dataarray;
	return "$myproxy/".fullyencode($host)."/";
}


/*
 * Decode the <BASE> appended request 
 */
function decodebaseRequest($url){
	global $myproxy, $myurldelimiter, $myinfodelimiter, $dataarray;
	//$url=str_replace("%5C", "\\", $url);
	if(preg_match("/([^\/]*)$myurldelimiter(\/.*)/ims", $url, $matches)!=0){
		$body=safeBase64_decode($matches[1]);
		if(($p1=strpos($body, $myinfodelimiter))>1){
			$url = substr($body, 0, $p1);
			$body = substr($body, $p1+strlen($myinfodelimiter));
			$dataarray=rawdecode($dataarray, $body);
		}
		else
			$url=$body;
			
		return replaceFunnyChars($url.$matches[2]);
	}
	else
		return NULL;	

}


/*
 * Encrypt and decrypt based on a String that acts like a key
 */
function xencrypt($string, $key) {
 	$result = '';
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	return $result;	
}
function xdecrypt($string, $key) {
  $result = '';

  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)-ord($keychar));
    $result.=$char;
  }

  return $result;
}


/*
 * Create the dataarray from the get Parameters.
 */
function makeDataarray($get,&$dataarray){
	global $myurlnameEnc, $myurlnamePlain, $myurlnameJSEnc, $mytrueurlnamePlain;

	$dataarray = $get;
	
	if(isset($dataarray[$myurlnameEnc])) unset($dataarray[$myurlnameEnc]);
	if(isset($dataarray[$myurlnamePlain])) unset($dataarray[$myurlnamePlain]);
	if(isset($dataarray[$myurlnameJSEnc])) unset($dataarray[$myurlnameJSEnc]);
	if(isset($dataarray[$mytrueurlnamePlain])) unset($dataarray[$mytrueurlnamePlain]);
}


/*
 * Simple encs to encode and decode javascript to plain hex
 */
function xencodecx($url){
	$out="";	
	for($i=0; $i<strlen($url); $i++)
		$out.= dechex(ord(substr($url, $i, 1)));
	return $out;	
}
function xdecodecx($out){
	$url="";
	for($i=0; $i<strlen($out); $i+=2)
		$url.= chr(hexdec(substr($out, $i, 2)));
	return $url;	
}
?>