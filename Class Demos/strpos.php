<html><head><title>StrPos Test</title></head>
  <body>
<?php
  $mystring = 'abc';
$findme = 'c';
$pos = strpos($mystring, $findme);
print "$pos<br>" . getType($pos) . "<br><br>";

$mystring = 'abc';
$findme = 'z';
$pos = strpos($mystring, $findme);
print "*" . setType($pos, "string") . "*<br>";
print "\$pos<br>" . getType($pos) . "<br><br>";
?>
  </body>
</html>
