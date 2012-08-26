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


$myhost				= "http://127.0.0.1/fast";

$mysecurityhtml		= "$myhost/prxprod";

$myerrorpage		= "$myhost/prxprod/html_error.php";
$myoriginalproxy	= "$myhost/prxprod/go";


/*
 * The offload proxies
 */
$_facebook		= "$myhost/facebookprod/go";
$_google		= "$myhost/googleprod/go";
$_bypassproxy	= "$myhost/bypassprod/go";


/*
 * Required for the Menu and pre-coding on proxified pages
 */
$mystylesheet		= "$myhost/prxprod/menu/menu.css";
$myjsencrypter		= "$myhost/prxprod/encrypter_js.php";


/*
 * Set the default loggin option to 0 (no logging)
 */
$loglevel=0;


/*
 * If true then bypass content filtering - faster response
 * Default add will be 'unclassified'
 */
define('STREAMLINE', true);


/*
 * Define the proxy details if you are using a proxy and if
 * the proxy requires username/password
 */
//define('USEPROXY', 	  "mytestproxy.com:3128");
//define('USEPROXYCREDS', "Barbie:IloveKen");

?>
