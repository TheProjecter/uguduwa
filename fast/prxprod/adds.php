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
 * Specify all the adds here. 
 */
$addprefix			= "http://127.0.0.1/fast/prxprod";
$addpageG 			= "$addprefix/ads/pageG1.html";
$addpageM 			= "$addprefix/ads/pageM1.html";
$addpageR 			= "$addprefix/ads/pageR1.html";
$addpageU 			= "$addprefix/ads/pageU1.html";
$externaladdpageG 	= "$addprefix/ads/pagexG1.html";
$externaladdpageM 	= "$addprefix/ads/pagexM1.html";


/*
 * Percentage of external adds to show
 * 0 = show only external adds, 10 = show only our ads.
 */
$percentintadds = 5;


/*
 * Get the relevant add based on the rating
 */
function getMyAdd($rating){
	global 	$addpageG,
	$addpageR,
	$addpageM,
	$addpageU;
	
	if($rating=="G")
		return $addpageG;
	else if($rating=="R")
		return $addpageR;
	else if($rating=="M")
		return $addpageM;
	else
		return $addpageU;	
}


/*
 * Get the external add based on the rating
 */
function getMyExternalAdd($rating){
	global 	$externaladdpageG,
	$externaladdpageM;
	
	if($rating=="G")
		return $externaladdpageG;
	else	
		return	$externaladdpageM;
}
?>
