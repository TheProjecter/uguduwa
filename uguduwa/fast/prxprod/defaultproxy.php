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


/*****Time stuff*****/
//$xtime	= 0;
//$xtime0 = microtime(true);

set_time_limit(120);

define('chost_cookie', "__uunchost");

/* Override the logs
 * 
 * 
 */
$loglevel = 0;

if(($loglevel & 1)==1) 	$reqf		= fopen("zrequests.txt", 'a');
if(($loglevel & 2)==2)	$difff		= fopen("zdiff.txt", 'a');
if(($loglevel & 4)==4)  $regxf		= fopen("zregexs.txt", 'a');
if(($loglevel & 8)==8) 	$outputf	= fopen("zbody.txt", 'a');


error_reporting(0);

include 'coders.php';
include 'guard.php';
include 'grokkers.php';
include 'regxs.php';
include 'headmaster.php';
include 'adds.php';
include 'postrequest.php';
include 'offloader.php';
include 'defaultopts.php';
include 'constants.php';
include 'blockedips.php';

$dataarray = array();

/*
 * The global variables;
 */
$currentHost;
$currentDirectory;
$postRequestTrue;
$httpStatus;
$sub_req_url="";
$original_req_url;
$output="";
$contentType="";
$headers="";

/* These are for the writefunction called via CURLOPT_WRITEFUNCTION */
$finishedheaders=0;
$redirectincallback=0;
$binarydata=0;

/* POST request */
$encoded="";


/*
 * test the guard cookie to make avoid phishing accusations.
 */
$securityok=testguardcookie($_SERVER['HTTP_COOKIE'], $_SERVER['REMOTE_ADDR']);


/*
 * Decode the URL
 */
if(isset($_GET[$myurlnameEnc])){
	if(!$securityok){
		header(makeAllHTTPS("Location: $mysecurityhtml/html_security.php?$myurlnameEnc=".$_GET[$myurlnameEnc]));
		exit(0);	
	}
	$original_req_url=$_GET[$myurlnameEnc];
	$sub_req_url=buildGetRequest("$myproxy?".$_SERVER['QUERY_STRING']);
}
else if(isset($_GET[$myurlnameJSEnc])){
	if(!$securityok){
		header(makeAllHTTPS("Location: $mysecurityhtml/html_security.php?".$_SERVER["QUERY_STRING"]));
		exit(0);	
	}
	$original_req_url=xdecodecx($_GET[$myurlnameJSEnc]);
	$sub_req_url=$original_req_url;
	makeDataarray($_GET, $dataarray);	
}
else if(isset($_GET[$myurlnamePlain])){
	if(!$securityok){
		header(makeAllHTTPS("Location: $mysecurityhtml/html_security.php?".$_SERVER["QUERY_STRING"]));
		exit(0);	
	}
	$original_req_url=$_GET[$myurlnamePlain];
	$sub_req_url=$original_req_url;
	makeDataarray($_GET, $dataarray);
}
else{
	$sub_req_url=decodebaseRequest($_SERVER['REQUEST_URI']);
	if($sub_req_url===NULL){
		if(preg_match("/".chost_cookie."=([^;]*)/is", $_SERVER['HTTP_COOKIE'], $matches)){
			$newurl= $matches[1].$_SERVER['REQUEST_URI'];
			header(makeAllHTTPS("Location: ".grokReplace($newurl,$newurl)));
			exit(0);
		}
		else{
			header(makeAllHTTPS("Location: $myerrorpage?e=1"));
			exit(0);
		}
	}

}

if(!isset($sub_req_url) || $sub_req_url == ""){
	header(makeAllHTTPS("Location: $myerrorpage?e=2"));
	exit(0);	
}
else{
	$newhost=getRedirectProxy($sub_req_url);
	if($newhost!=NULL){
		header(makeAllHTTPS("Location: $newhost?$myurlnameEnc=".fullyencode($sub_req_url)));
		exit(0);
	}
	$sub_req_url = str_replace(" ", "%20", $sub_req_url);

}


/*
 * Stop it immediately if it's in the blockedurl list.
 */
foreach($blockedurls as $stopurl){
	if(stripos($sub_req_url, $stopurl)!==FALSE){
		exit(0);
	}
}


/*
 * Initialize curl !!!!!!!
 */
$ch = curl_init();


/*I* Special curl options Interface to be implemented by calling file *I*/
if(myspecialcurloptionsI($ch, $sub_req_url)){
	setdefaultoptions($ch, $_SERVER, $sub_req_url);
	curl_setopt($ch, CURLOPT_REFERER, $sub_req_url );
	curl_setopt($ch, CURLOPT_URL, $sub_req_url);
}


/*
 * If it's a post request then we have to set up POST REQUEST in curl.
 */
if(strstr($_SERVER['REQUEST_METHOD'], "POST")){

	$encoded = getencodedpost($_POST);
	
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
}


/*
 * EXECUTE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */
if(($x=curl_exec($ch))===FALSE){
	header(makeAllHTTPS("Location: $myerrorpage?e=3&ce=".curl_errno($ch)));
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
		
			grokSetCookies($headers);

			if(isset($contentType) && $contentType!=""){
				header("Content-type: $contentType");
				
				if(!preg_match("/\b(text|javascript|xhtml)\b/ims", $contentType)){
					$binarydata=1;	
				}
			}
			else
				$binary=0;
			if($httpStatus>=300 && $httpStatus<400){
				$redirectincallback=1;
			}

			/*what is left*/
			echo $output;
 		}
 	}

	return strlen($data);	
}


/*
 * Set up the current Host and Directory
 */
$currentHost=$sub_req_url;
$currentDirectory=$sub_req_url;
grokHostAndDir($sub_req_url, $currentHost,$currentDirectory);


/*
 * If there was a redirrect in thewritefunction handle it here.
 */
if($redirectincallback){
	$loc=grokLocation($headers);
	header(makeAllHTTPS("Location: $loc"));
	exit(0);
}


/*
 * Close the curl connection
 */
curl_close($ch);


if($binarydata){
	//do nothing at the moment
}
else{ /*BIGELSE*/
/*
 * This section should only get called for text/javascript cocntent
 */


	/*
	 * Extract the current host and current directory for pre-pend
 	 * AND start the compression stream if it's text.
 	 */
	$rating="";

	ob_start("ob_gzhandler");
 	//4 is the default!!!!
	//ob_start(array('ob_gzhandler',9));

	
	/*Override the current host and directory if there is a <base> tag*/
 	if(preg_match("/<\s*base\s+href\s*=\s*([\"'])([^\"'<>]+?)\\1\s*\/?\s*>.*?(<\s*\/\s*base\s*>)*/ims", $output, $matches)){
		$output=str_replace($matches[0],"",$output);
		grokHostAndDir($matches[2],$currentHost,$currentDirectory);
	}

		
	/* Log urls's and any 'post' params */
	if($loglevel>=4)
		fwrite($reqf, $_SERVER['REQUEST_METHOD']." $httpStatus $sub_req_url $encoded\n");

	

	/*set the cookie to the readable page*/	
	setrawcookie(chost_cookie, $currentHost, 0, "/");
		
	if(($loglevel & 2) == 2){
		$original_output_length = strlen($output);
		$original_output = $output;
	}	

	/*****Time stuff*****/
	$xtime = microtime(true);
	
	/*I* Special grokkers Interface to be implemented by calling file *I*/
	if(myspecialgrokkerI($output, $sub_req_url)){
		$output	= preg_replace_callback(SRC_REGX, 	"grokSrcReplace" , 		$output);
		$output	= preg_replace_callback(URL_REGX, 	"grokUrlReplace" , 		$output);
		$output	= preg_replace_callback(IMPORT_REGX,"grokUrlReplace" , 		$output);
		$output = preg_replace_callback(JSQ_REGX,	"grokScriptReplace", 	$output);
		$output = preg_replace_callback(JSQ_REGXSRC,"grokScriptSrcReplace", $output);
		$output = preg_replace_callback(HTTP_REGX, 	"grokHttpReplace", 		$output);
		$output = preg_replace_callback(JS_EVAL, 	"grokChangeEval", 		$output);
		//$output = preg_replace_callback(FLASH_REGX, "grokFlashvarsReplace", $output);
		
	}
	if(($loglevel & 2) == 2){
		if(strlen($output)!=$original_output_length)
			fwrite($difff, "$sub_req_url\n");
	}
	if(($loglevel & 8) == 8){
		fwrite($outputf, "$sub_req_url\n----------\n$headers\n----------\n");
			fwrite($outputf, "$original_output\n\n\n***********************\n\n\n");
	}
	
	/* Add the header to html requests */
	if(preg_match("/\b(html)\b/ims", $contentType)){
		$output=addHeadMaster($output, $sub_req_url, $rating);
	}
		
	/*****Time stuff*****/
	$xtime = round(microtime(true)-$xtime, 3);

 	/* Add the base tag to 'collect' the half zombies*/
	$output=preg_replace("/<\s*head\s*>/ims","<head><base href=\"".encodeBaseTag($currentDirectory)."\"/>", $output);


	/* If https convert all URLs to point to https */
	$output=makeAllHTTPS($output);


	/*
	 * Send the output back to the client !!!!!!!!!!!!!!!!!!!!!
	 */
	echo $output;


	/*
	 * Flush all the output
	 */
	ob_end_flush();


} /*BIGELSE*/


/*
 * Close all the log file if you opened them.
 */
if(($loglevel & 1)==1)fclose($reqf);
if(($loglevel & 2)==2)fclose($difff);
if(($loglevel & 4)==4)fclose( $regxf);
if(($loglevel & 8)==8)fclose($outputf);

//$xtime0 = round(microtime(true)-$xtime0, 3);
//error_log("Get: $rating $xtime/$xtime0 $sub_req_url");


?>

