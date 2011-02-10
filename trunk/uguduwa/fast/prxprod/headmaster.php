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


include 'contentfilter.php';


/*
 * Adds the top banner on proxified pages. 
 */
function addHeadMaster($output, $sub_req_url, &$rating){
	global $percentintadds,  $myhost, $dataarray,
	$mystylesheet, $myjsencrypter, $myoriginalproxy;
	
	/*extract the user values from the url*/
	$bgcolor=$dataarray[MYBGCOLORTAG];
	$addurl=$dataarray[MYADDURLTAG];
	$sanitize=$dataarray[SANITIZETAG];
	
	
	if(isset($sanitize))
		$sanitize=intval($sanitize);	
	else
		$sanitize=0;
	
	$divheight;
	$adpage;
	$ratingicon;
	$httpsicon = "";

	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=="on"){
		$myoriginalproxy = str_replace("http", "https", $myoriginalproxy);
		$httpsicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:#cc9900\\\">&nbsp;HTTPS-ON&nbsp;</font>";
	}
	
	if(STREAMLINE){
		$rating = "U";	
	}
	else{
		$rating=getContentRating($output, $sanitize);
	}
	
	
	if(isset($addurl) && $addurl!=""){
		if(isset($bgcolor) && $bgcolor!="")
			$addbgcolor=getLightColor($bgcolor);
		else
			$bgcolor="";
		
		$divheight	= 120;
		$external=1;

		$randomn= rand(1,10);
		
		if($randomn > $percentintadds && !preg_match("/page[GMRUx]([GM])*\d\.html/ims",$addurl) )
			$adpage= getMyAdd($rating);
		else
			$adpage=getMyExternalAdd($rating).($bgcolor==""?"":"?col=$addbgcolor");
	}
	else{
		$bgcolor	= "";
		$external	= 0;
		$divheight	= 92;

		$adpage=getMyAdd($rating);
	}
	
	
	if($rating=="G")
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:green\\\">&nbsp;G&nbsp;</font>";
	else if($rating=="M")
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:blue\\\">&nbsp;M&nbsp;</font>";	
	else if($rating=="R")
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:red\\\">&nbsp;R&nbsp;</font>";	
	else
		$ratingicon="<font class=\\\"prxftxt\\\" style=\\\"background-color:black\\\">&nbsp;U&nbsp;</font>";

	$hideheight	= 27;
	$hidewidth = 50;
	$totaldivheight=($divheight+40);

	$colorstring = $bgcolor==""?"":"background-color:#$bgcolor";
		
	$displayurl = preg_replace("/^http:\/\//ims", "", $sub_req_url);
	
	$head  =	"<div id=\"add_place_dvx\" style=\"height:".$totaldivheight."px;overflow:hidden\"></div>".
			"<div scrolling=\"no\" style=\"margin:0 0 0 0;z-index:1000;overflow:hidden;position:absolute;top:0px;left:0px;width:100%;\" id=\"add_link_dvx\"></div>".
			"<script>".
			"function xsubmitx(){document.getElementById('".URLNAMEJSENC."').value=xencodecx(document.getElementById('xu').value);}".
			"if(top.location==self.location){".
			"var x=document.getElementById('add_link_dvx');x.style.height=\"".$totaldivheight."px\";x.visibility=\"visible\";".
			"x.innerHTML=\"<table class=\\\"prxtbl\\\" style=\\\"$colorstring\\\">".
			"<tr><td><table class=\\\"prxtbl\\\" style=\\\"$colorstring\\\"><tr><td width=\\\"1%\\\"><span class=\\\"prxflnk\\\" id=\\\"add_show_dvx\\\"></span></td><td width=\\\"1%\\\"><form onsubmit=\\\"xsubmitx()\\\" target=\\\"_top\\\" class=\\\"prxf\\\" target=\\\"_top\\\" action=\\\"".$myoriginalproxy."\\\"><table style=\\\"$colorstring\\\" class=\\\"prxtbl\\\"><tr><td><b>Enter url</b></td><td>".
/*pln*/		//"<input type=\\\"text\\\" value=\\\"$sub_req_url\\\" id=\\\"".URLNAMEPLAIN"."\\\" class=\\\"prxfinbox\\\" size=\\\"75\\\"/>".
			"<input type=\\\"text\\\" value=\\\"$displayurl\\\" id=\\\"xu\\\" class=\\\"prxfinbox\\\" size=\\\"75\\\"/>".
			"<input type=\\\"hidden\\\" value=\\\"$sub_req_url\\\" id=\\\"".URLNAMEJSENC."\\\" name=\\\"".URLNAMEJSENC."\\\"/>".
		
			($external?"<input type=\\\"hidden\\\" name=\\\"".MYADDURLTAG."\\\" value=\\\"".$addurl."\\\"/>":"").
		 	($bgcolor?"<input type=\\\"hidden\\\" name=\\\"".MYBGCOLORTAG."\\\" value=\\\"".$bgcolor."\\\"/>":"").
/*san*/		//"</td><td><input title=\\\"Tick this to remove 'bad words' and sanitize websites. You may need this to bypass internet filters such as NetNanny etc, or filters that use deep packet inspection. May cause web page rendering problems\\\" type=\\\"checkbox\\\" class=\\\"prxfinbut\\\" name=\\\"".SANITIZETAG."\\\" value=\\\"1\\\" ".($sanitize?"CHECKED":"")." />".
		
			"</td><td><input type=\\\"submit\\\" class=\\\"prxfinbut\\\" value=\\\"submit\\\"/></td></tr></table></form></td><td width=\\\"1%\\\">&nbsp;&nbsp; $ratingicon  </td><td width=\\\"1%\\\">&nbsp;&nbsp; $httpsicon  </td><td width=\\\"*\\\">&nbsp;</td><td width=\\\"1%\\\"><span class=\\\"prxflnk\\\" id=\\\"add_hide_dvx\\\"><a class=\\\"prxflnk\\\" href=\\\"javascript:hidemeplease()\\\">Hide!</a></span></td><td width=\\\"%1\\\">".($external?"&nbsp;&nbsp;&nbsp;":"<img style=\\\"text-align:right;border:0\\\" src=\\\"$myhost/prxprod/menu/ico_prxycore.gif\\\"/>")."</td></tr></table></td></tr>".
/*add*/		"<tr><td><iframe id=\\\"add_link_ifrmx\\\" frameborder=\\\"0\\\" scrolling=\\\"no\\\" class=\\\"prxifrm\\\" src=\\\"$adpage\\\"></iframe></td></tr></table>\";".
			"document.getElementById('add_link_ifrmx').style.height=\"".$divheight."px\";".
			"}else{;".
			"document.getElementById('add_place_dvx').style.height=\"0px\";}".
			"function hidemeplease(){var x=document.getElementById('add_link_dvx');x.style.height=\"".$hideheight."px\";x.style.width=\"".$hidewidth."px\";var y=document.getElementById('add_place_dvx');y.style.height=\"".$hideheight."px\";y.style.width=\"".$hidewidth."px\"; document.getElementById('add_hide_dvx').innerHTML=\"\";document.getElementById('add_show_dvx').innerHTML=\"<left><a class=\\\"prxflnk\\\" href=\\\"javascript:showmeplease()\\\">Show!</a>&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/></left>\";  }".
			"function showmeplease(){var x=document.getElementById('add_link_dvx');x.style.height=\"".$totaldivheight."px\";x.style.width=\"100%\";var y=document.getElementById('add_place_dvx');y.style.height=\"".$totaldivheight."px\";y.style.width=\"100%\";document.getElementById('add_show_dvx').innerHTML=\"\";document.getElementById('add_hide_dvx').innerHTML=\"<a class=\\\"prxflnk\\\" href=\\\"javascript:hidemeplease()\\\">Hide!</a>\";}".
			"</script>";
	
	$output=preg_replace("/<\s*head\s*>/ims", 
		"<head><link rel=\"stylesheet\" type=\"text/css\" href=\"$mystylesheet\" />".
		"<script type=\"text/javascript\" src=\"$myjsencrypter\"></script>"
		, $output);
	
	if(preg_match(SRC_REGX_BODYTAG, $output, $matches))
		return str_replace($matches[0], $matches[0].$head, $output);
	else
		return $output;
}


/*
 * Generate light color for external adpages 
 */
function getLightColor($x){
	if(preg_match("/([0-9a-fA-F][0-9a-fA-F])([0-9a-fA-F][0-9a-fA-F])([0-9a-fA-F][0-9a-fA-F])/ims", $x, $matches)){
		$r=(255+hexdec($matches[1]))/2;
		$b=(255+hexdec($matches[2]))/2;
		$g=(255+hexdec($matches[3]))/2;
		
		$r=($r>255?255:$r);
		$b=($b>255?255:$b);
		$g=($g>255?255:$g);
		
		return dechex($r).dechex($b).dechex($g);
	}
	else
		return "";

}

?>