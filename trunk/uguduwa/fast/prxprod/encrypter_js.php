function xencodecx(x){
	out="";
	for(i=0;i<x.length;i++)
		out+=x.charCodeAt(i).toString(16);
	return out;
}
function xdecodecx(x){
	out="";
	for(i=0;i<x.length;i+=2)
		out+=String.fromCharCode(parseInt(x.substr(i,2), 16));
	return out;
}

function peval(x){
	var temp="<?php 
	include "host.php";
	include "constants.php";
	echo $myoriginalproxy;
	?>";
	
	var temp2="<?php echo $myurlnamePlain?>";
	
	regx = /http:\/\/[^'\"\s]*/gi;
	urls = x.match(regx);
	for(i = 0 ; i< urls.length ;i++){
		x=x.replace(urls[i], temp+"?"+temp2+"="+urls[i]);	
	}
	eval(x);	
}