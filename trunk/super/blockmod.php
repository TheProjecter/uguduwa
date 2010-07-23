<?php

$m = array();
$n = array();

$f = fopen($argv[1], "r");
while(!feof($f)){
	$s=fgets($f, 4096);
	if( preg_match("/(\d+\.\d+\.\d+\.\d+).*/ims", $s, $matches)){
		if(preg_match("/page[GMRUx]([GM])*1\.html HTTP/ims", $s, $matches2)){
			if($n[$matches[1]]==NULL)
				$n[$matches[1]]=1;
			else
				$n[$matches[1]]++;
		}
		if($m[$matches[1]]==NULL)
			$m[$matches[1]]=1;
		else
			$m[$matches[1]]++;
	}
}
fclose($f);

asort($m);

$rm = array_reverse( $m, TRUE);

$str="\"du.m.my\"=>\"\",\n";
foreach($rm as $ip=>$count){
	if($n[$ip]==0 && $count>3000){
		$str.="\"$ip\"=>\"\",\n";

	}
}

$bfile="/var/www2/html/blockedips.php";
$b = fopen($bfile, "r");
$bcontent = fread($b, filesize($bfile));
fclose($b);
preg_match("/(\/\*_\*\/)(.*?)\\1/ims",$bcontent, $matches);
$bcontent=str_replace($matches[2], "\n$str", $bcontent);


$b = fopen($bfile, "w");
fwrite($b,$bcontent);
fclose($b);




?>

