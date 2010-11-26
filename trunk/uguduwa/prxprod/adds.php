<?php
/*
    Code Diaries Proxy Core - Web Proxy
    Copyright (C) 2010  Ping Shin Ching

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/licenses.

	Ping Shin Ching
	righteous.ninja@gmail.com OR ping_sc@hotmail.com
	More information at http://codediaries.com
 */


/*
 * Specify all the adds here. 
 */
$internaladdpage 	= "http://127.0.0.1/php/fast/prxprod/ads/page1.html";
$addpageG 			= "http://127.0.0.1/php/fast/prxprod/ads/pageG1.html";
$addpageM 			= "http://127.0.0.1/php/fast/prxprod/ads/pageM1.html";
$addpageR 			= "http://127.0.0.1/php/fast/prxprod/ads/pageR1.html";
$addpageU 			= "http://127.0.0.1/php/fast/prxprod/ads/pageU1.html";
$externaladdpageG 	= "http://127.0.0.1/php/fast/prxprod/ads/pagexG1.html";
$externaladdpageM 	= "http://127.0.0.1/php/fast/prxprod/ads/pagexM1.html";


/*
 * Percentage of our adds to show 
 */
$percentintadds = 0;


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
