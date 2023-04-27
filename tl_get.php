<?php
$qs = $_SERVER["QUERY_STRING"];
[ $tlname, $tlepisode ] = explode("/",$qs);
$tlstub = "tl_${tlname}";
$tldir = "tl/$tlname";
if( ! is_dir( $tldir ) ) {
  echo "tl dir $qs does not exist\n";
  exit();
}
if( is_file($fn = "${tldir}/tl_${tlname}_${tlepisode}.txt") ) {
  $a = file_get_contents($fn);
  $xs = explode("\n",$a,3);
  $t = $xs[0];
  $tl = $xs[2];
  $rv = [ "title" => $t, "tracklist" => $tl ];
} else {
  $rv = [ "error" => "Episode ${qs} not found." ];
}
$j = json_encode($rv);
echo $j;
