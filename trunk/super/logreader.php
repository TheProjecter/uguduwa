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
asort($m);

$rm = array_reverse( $m, TRUE);

//print_r($rm);
$totalcount=0;
$totaladds=0;
foreach($rm as $ip=>$count){
	echo "$ip\t\t$count\t\t".$n["$ip"]."\n";
	$totalcount+=$count;
	$totaladds+=$n["$ip"];
}
echo "\t\t\t$totalcount\t\t$totaladds ".($totaladds*100/$totalcount);
fclose($f);

?>

