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
 * Recursively get and map out all the post requests. This requires
 * because of the embedded arrays inside post strings.
 */
function getencodedpost($post){
	$encoded="";
	recursiveposter($post,"", $encoded);
	$encoded = substr($encoded, 0, strlen($encoded)-1);
	return $encoded;
}
function recursiveposter($array, $prepend, &$finalstring){
	foreach($array as $name => $value) {
		if(is_array($value)){
			recursiveposter($value, "$prepend$name|", $finalstring);
		}
		else{
			$myname="$prepend$name|";
			$matches=preg_split("/\|/ims", $myname);
			unset($matches[count($matches)-1]);
			
			$str=current($matches);
			while(($n = next($matches)) !==FALSE) {
					$str.="[$n]";
			}
			$finalstring.=urlencode($str)."=".urlencode($value)."&";
		}
	}
}
function checkpostforspam($post){
	$p = urldecode($post);
	if(preg_match("/[a-zA-Z-]+(\.[a-zA-Z-]+)+/ims", $p))
		return 1;
	else 
		return 0;
}
?>