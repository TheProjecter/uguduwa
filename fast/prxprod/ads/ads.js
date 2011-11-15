function MyGetUrlParams(){
	var url_params = new Array();
	var urla = location.href.substring(location.href.indexOf('?')+1).split('&');
	for(i=0;i< urla.length ;i++){
		url_params[urla[i].substring(0,urla[i].indexOf('='))]=
		urla[i].substring(urla[i].indexOf('=')+1);
	}
	return url_params;
}
if(MyGetUrlParams()['col']!=null && MyGetUrlParams()['col']!="0"){
	document.body.style.background="#"+(MyGetUrlParams()['col']);
}
