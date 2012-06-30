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


include 'host.php';
include 'grokkers.php';
?>
<html>
<head>
<title>Code Diaries Core Error</title>
<link rel="stylesheet" type="text/css" href="index.css" />
<link rel="stylesheet" type="text/css" href="menu/menu.css" />
<script type="text/javascript" src="js_encrypter.php"></script>
<script>
function xsubmitx(){
	document.getElementById('urlX').value=xencodecx(document.getElementById('url').value);
	return true;
}
</script>
</head>
<body>
&nbsp;<br/>
<center>

<table cellpadding="10" style="width:60%;background-color:#e8e8e8;border-style:solid;border-width:1px;border-color:#999999">
<tr><td>
</td></tr>
<tr><td>
<?php if ($_GET['e']==3){ ?>
	<table><tr><td><h2>Oops! This operation is blocked</h2></td></tr></table>
	<b>Because this is a free proxy some protocols, urls, ports, methods and host may be blocked for your safety and ours.</b><br/><br/>
	Please bear in mind that this is a web-page based proxy and therefore we need to have strict measure to prevent abuse.
	If you could leave a comment via the link below, it will be greatly appreciated. 
<?php }else{ ?>

	<table><tr><td><h2>Oops! It looks like we hit an error</h2></td></tr></table>
	<b>Either this proxy has encountered an internal error or there is a problem with the site you are trying to access.</b><br/><br/>
	Please bear in mind that this is a web-page based proxy and therefore has some limitations.
	If you could leave a comment via the link below, it will be greatly appreciated.

<?php } ?>
</td></tr>
<tr><td>
	<table><tr><td>
	<input type="button" onClick="history.go(-1)" class="prxfinbut" value="go back"/>
	<!-- 
	<form onsubmit="xsubmitx()" class="PRXform" target="_top" 
	action="<?php 
		echo makeAllHTTPS($myoriginalproxy);	
	?>">
	<table class="PRXformHeader"><tr><td><b>Enter url</b> </td><td> <input type="text" id="url" class="prxfinbox" size="60"/> 
	<input type="hidden" name="urlX" id="urlX" /> 
	</td><td><input type="submit" class="prxfinbut" value="submit"/></td></tr></table>
	</form>
	 -->
	</td></tr></table>
</td></tr>
<tr><td>
<a target="_top" href='http://code.google.com/p/codediariesproxy/issues/list'>Please leave a description of the Issue here</a>
 </td></tr>
</table>
</center>
</body>
</html>