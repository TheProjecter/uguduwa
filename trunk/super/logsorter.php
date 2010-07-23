<html><body>
<?php

$m = array();
$n = array();

if(($f = fopen($argv[1], "r"))==FALSE){
	echo "file error";
	exit(0);
}
$totalcount=0;
while(!feof($f)){
	$s=fgets($f, 4096);
	if( preg_match("/headmaster: [GMRU] http:\/\/[^\/\?&]*(\b\w+\.\w+).*/ims", $s, $matches)){
		$totalcount++;
		if(!isset($m[$matches[1]]))
			$m[$matches[1]]=1;
		else
			$m[$matches[1]]++;
	}
}
fclose($f);
asort($m);

$rm = array_reverse( $m, TRUE);


$mostk=key(array_slice($rm, 0, 1));
$toptenk=key(array_slice($rm, 8, 1));


$most=$rm[$mostk];
$topten=$rm[$toptenk];

$resttotal=0;


$chl="";
$chd="t:";

foreach($rm as $ip=>$count){
	if($count < $topten)
		$resttotal+=$count;	
	else{
		$chl.="$ip ".round($count*100/$totalcount)."%|";
		$chd.="$count,";
	}
}

$chl.="rest of the sites ".round($resttotal*100/$totalcount)."%";
$chd.="$resttotal";

echo "<img src=\"http://chart.apis.google.com/chart?chs=450x200&cht=p&chds=0,100000&chd=$chd&chl=$chl\"/>";
echo "$totalcount";
?>
</body>
</html>
