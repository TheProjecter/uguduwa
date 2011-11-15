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
 * Get the content rating. 
 * Return Unclassified if content cannot be rated (a language other than english)
 */
function getContentRating(& $original_output, $sanitize=0){

/*really should be constants*/	
$ENGLISH_SCORE=10;
$CONTENT_M_SCORE=5;
$CONTENT_R_SCORE=20;
$MAX_SEARCH_LENGTH=50000;
	
	//Words to see if the page is in english
	$cat0 = array ( 
				" the ",
				" to ",
				" of ",
				" you ",
				" in ",
				" is ",
				" that ",
				" was ",
				" he ",
				" for ",
				" on ",
				" as ",
				" are ",
				" with ",
				" this "
				);
	
	
	//Words that lead to an immediate ban
	$cat1 =	array(	
	/*Killing*/	"dismember",		
				"maimed",
				"disembowel",
				"slaughter",
				"vicera",
				"paralyz",
				"snuff movie",
				"snuff film",
	
	/*Porn*/	"twink",
				"cocksucker",
				"cock sucker",
				"blowjob",
				"blow job",
				"gangbang",
				"gang bang",
				" cum ",
				"porn",
				"fuck",
				"cunt",
				"dildo",
				"slut"
				);
	//Aggresive words but will tolerate 3			
	$cat2 = array(	
	/*Killing*/	"disfigure",
				"dying",
				"decapitat",
				"deceased",
				"butcher",
				"execution",
				"drown",
				"strangle",
				"electrocution",
				" hanging",
				"murder",
				"snuff",
				"suicide",
				"entrails",
				"corpse",
				"cadaver",
	
	/*Porn*/	" rape",
				" raping",
				"rapist",
				"deflower",
				"defloration",	
				"milf",
				"vagina",
				"horny",
				"pussy",
				"erection",
				"masturbation",
				"pedophil",
				"whore",
				"semen ",
				"penis",
				"testicles",
				"fellatio",
				"cunnilingus",
				"coproph",
				"feces",
				"fecal",
	
	/*Drugs*/	"cocaine",
				"marijuana",
				"heroin"
				);
	
	//Dirty words and slang, ok if not used too often			
	$cat3 = array(	
	/*Killing*/	"dead",
				"death",
				" kill",
	
	/*Porn*/	"shit",
				"screw",
				"bitch",
				" anal ",
				"orgy",
				"pregnant",
				"homosexual",
				" gay ",
				"virgin",
				"lesbian",
				"nude",
				"wife",
				"girlfriend",
				"naked",
				"nudity",
				"fetish",
				"panty",
				"panties",
				" sex",
				"sexy",
				"explicit",
				
	/*Hacking*/	"hacking",
				"cracking",
				"hacker",
				"warez",
				
	/*Gambling*/"gambl",
				"poker",
				"casino",
	
	/*Drugs*/	"drug",
				"steroid",
				"medication",
				"pharmac",
				"abortion",
				"beer",
				"liquor",
				"alcohol",
				"booze",	
	
	/*weapons*/ "knife",
				"knives",
				" gun ",
				"weapon",
				" ammo ",
				"ammunition",
				"bombs",
				"explosive",
				"terrorism",
	
	/*Racial*/	"nazi",
				"jew",
				"nigger"
				
				);
	$sanitize=0;
				
	$pointscore=0;
	$englishscore=0;

	/*****Time stuff*****/
	//$xtime = microtime(true);

	$output=strtolower(substr($original_output, 0, $MAX_SEARCH_LENGTH));
	$output=str_replace(array(".", "\n", ">"), " ", $output);
	
	
	foreach($cat1 as $catval){
		$pointscore+=(substr_count($output, $catval)*100);
		if($sanitize)
			$original_output=str_ireplace($catval, "*1*$catval**", $original_output);
	}
	if($pointscore < $CONTENT_R_SCORE){
		foreach($cat2 as $catval){
			$pointscore+=(substr_count($output, $catval)*2);
			if($sanitize)
				$original_output=str_ireplace($catval, "*2*$catval**", $original_output);
		}
		if($pointscore < $CONTENT_R_SCORE){	
			foreach($cat3 as $catval){
				$pointscore+=substr_count($output, $catval);
				if($sanitize){
					$original_output=str_ireplace($catval, "*3*$catval**", $original_output);
				}
			}	
		}
	}
	foreach($cat0 as $catval){	
		$englishscore+=substr_count($output, $catval);
	}

	/*****Time stuff*****/
	//$xtime = microtime(true)-$xtime;
	//error_log("Time $xtime");
	
	if($pointscore>$CONTENT_R_SCORE)
		return "R";
	if($pointscore>$CONTENT_M_SCORE)
		return "M";	
	else{
		if($englishscore>$ENGLISH_SCORE)
			return "G";		
		else
			return "U";	
	}
}

?>
