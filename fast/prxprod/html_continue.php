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


include "guard.php";
include "coders.php";
include "constants.php";
include "host.php";
include "grokkers.php";


/*
 * Setup the gaurd cookie to prevent phishing accusations.
 */
setrawcookie($security_cookie, getguardcookie($_SERVER['REMOTE_ADDR']), 0, "/");
header(makeAllHTTPS("Location: $myoriginalproxy?".$_SERVER['QUERY_STRING'])); 
?>