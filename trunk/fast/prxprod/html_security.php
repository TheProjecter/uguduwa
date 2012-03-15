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
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="index.css" />
<script type="text/javascript" src="js_encrypter.php" ></script>
</head>
<body>
&nbsp;
<center>
<table cellpadding="20" style="background-color:#e8e8e8;border-style:solid;border-width:1px;border-color:#999999"><tr><td>
<center>

<?php
if(isset($_GET[URLNAMEENC])){
?>
	It looks like you have come here through a link somebody sent you.
	<p>&nbsp;</p>
	<a href="http://codediaries.com">Get me out of here</a>
<?php 	
}
else if(isset($_GET[URLNAMEJSENC])){
?>	

You are about to surf to <b><script>document.write(xdecodecx('<?php echo $_GET[URLNAMEJSENC]; ?>'));</script></b> via the Code Diaries Proxy.
	<p>&nbsp;</p>
	
	<a href="<?php echo "$mysecurityhtml/html_exit.php"; ?>"><font style="font-weight:bold;color:red">Get me out of here</font></a><br/>
	<font size="0.5em">I have no idea how I got here and I don't want to go here.</font>
	<p>&nbsp;</p>

	<a href="<?php echo makeAllHTTPS("$mysecurityhtml/html_continue.php?".$_SERVER['QUERY_STRING']); ?>">
	<font style="color:green;font-weight:bold">Continue</font></a> <br/>
			
	<font size="0.5em">I agree to the <a href="javascript:void(0)" onclick="window.open('terms_and_conditions.html','Terms_and_Conditions','scrollbars=yes,menubar=no,width=600,height=400,toolbar=no')">terms and conditions</a> of this proxy.</font>
			
	<p>&nbsp;</p>

<?php 	
}
else if(isset($_GET[URLNAMEPLAIN])){
?>	

You are about to surf to <b><?php echo $_GET[URLNAMEPLAIN]; ?></b> via the Code Diaries Proxy.
	<p>&nbsp;</p>
	
	<a href="<?php echo"$mysecurityhtml/html_exit.php"; ?>"><font style="font-weight:bold;color:red">Get me out of here</font></a><br/>
	<font size="0.5em">I have no idea how I got here and I don't want to go here.</font>
	<p>&nbsp;</p>

	<a href="<?php echo makeAllHTTPS("$mysecurityhtml/html_continue.php?".$_SERVER['QUERY_STRING']); ?>">
	<font style="color:green;font-weight:bold">Continue</font></a> <br/>
			
	<font size="0.5em">I agree to the <a href="javascript:void(0)" onclick="window.open('terms_and_conditions.html','Terms_and_Conditions','scrollbars=yes,menubar=no,width=600,height=400,toolbar=no')">terms and conditions</a> of this proxy.</font>
			
	<p>&nbsp;</p>
			
<?php	
}
else{
?>

Looks like there is an error.
<p>&nbsp;</p>
<a href="http://codediaries.com">Get me out of here</a>

<?php 
}
?>
</center>
</td></tr></table>
<font size="0.75em">It you keep seeing this page, make sure your cookies are enabled.</font>
</center>
</body>
</html>