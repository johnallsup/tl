<!DOCTYPE html>
<html>
<head>
<meta charset='utf8'/>
<title>Track listings</title>
<link rel="icon" href="tracklist-icon.png"/> 
<style>
body {
  margin: 0px;
  padding: 0px;
  background: url(marble2.png), #000000 linear-gradient(to bottom, #ed4264 0%, #ffedbc 50%, #000000 100%) no-repeat;
  font-family: Optima, Futura, Helvetica, Arial;
}
header {
  text-align: center;
  background-color: #005;
  color: white;
  margin: 0px;
  padding-top: 1vh;
  padding-bottom: 1vh;
}
div.container {
  display: flex;
  font-size: 18pt;
  margin: 2vh;
  border-radius: 2vh;
  background-color: white;  
  padding: 2vh;
  border: 1px solid black;
  box-shadow: 1vh 1vh 1vh black;
}
a {
  display: inline-block;
  color: black;
  margin: 1em;
  padding: 1em;
  border-radius: 0.5em;
  box-shadow: 0.3em 0.3em 0.3em black;
  border: 1px solid black;
  background-color: #ffc;
  text-decoration: none;
}
</style>
</head>
<body>
<header><h1>Track Listings</h1></header>
<div class='container'>
<?php
function li($a,$b) {
  echo "<a href='tl.php?$a'>$b</a>\n";
}
$a = file_get_contents("tl/index.txt");
$a = explode("\n",$a);
foreach($a as $x) {
  if( preg_match("/^(.*?): (.*)$/",$x,$m) ) {
    $short = $m[1];
    $full = $m[2];
    li($short,$full);
  }
} 
?>
</div>
</body>
</html>
