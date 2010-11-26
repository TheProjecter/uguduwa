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


/* href|src|action */
define('SRC_TAG_ATTR', 		"([A-Za-z][\w-_:\.]*\s*=\s*((\"[^\"<>]*\")|('[^'<>]*')|([^\s\"'<>]*))\s*)");
/*any tag*/
define('SRC_REGX', 			"/<\s*[A-Za-z][\w-_:\.]*\s*".SRC_TAG_ATTR."*\s*\/?\s*>/ims");

/*BODY tag*/
define('SRC_REGX_BODYTAG',	"/<\s*body\s*".SRC_TAG_ATTR."*\s*\/?\s*>/ims");
define('SRC_REGX_BODYTAG_END',	"/<\s*\/\s*body\s*>/ims");

/* url("...") */
define('URL_REGX',		"/[^\w](url)\s*\(\s*([\"']?)([^\)\"'\+,]+?)\\2\s*\)/ims");

/* @import "..."*/
define('IMPORT_REGX', 	"/(import\s+)(\\\\*[\"|'])([^\"']+?)\\2/ims");

/* js quoted string literals */
define('JSQ_REGX',		"/(\\\\*[\"|'])(https?(:(\\\\*\/)\\4|\\\\x3a\\\\x2f\\\\x2f)[^\"']+?\.[^\"']+?)\\1/ims");
define('JSQ_REGXSRC',	"/(href|src|action|code)\s*=\s*(\\\\+[\"'])([^\"']*)\\2/ims");

/* all http references */
define('HTTP_REGX',		"/(https?(:\/\/)[^\"'\s\)\>\\\\;:&]+?)/imsU");


/*Convert flash vars -- under construction*/
define('FLASH_REGX',	"/(http%3a(%2f%2f|%5c%2f%5c%2f)[^\&\"]*)(\&|\")/imsU");


//True Proxy Settings
define('TRUEHTTPS_REGX',	"/(https:(\\\\*\/)\\2)/imsU");


/*DIV tag*/
define('SRC_REGX_DIVTAG',	"/<\s*div\s*".SRC_TAG_ATTR."*\s*\/?\s*>/ims");
define('SRC_REGX_DIVTAG_END',	"/<\s*\/\s*div\s*>/ims");
/*TABLE tag*/
define('SRC_REGX_TABLETAG',	"/<\s*table\s*".SRC_TAG_ATTR."*\s*\/?\s*>/ims");
define('SRC_REGX_TABLETAG_END',	"/<\s*\/\s*table\s*>/ims");

define('JS_EVAL',"/eval\(/ims");
/*
 * What about open() (XMLHTTPRequest.open()) and replace() (document.replace())?
 */
?>