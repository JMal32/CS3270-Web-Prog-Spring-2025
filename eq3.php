<?php
echo "<pre>"'
print_r($_POST);
echo "</pre><br><br>";
$var = $_POST['txtInput'];
$a = "a";
$pos = strpos($var, $a);
$cnt = 0;
while (pos !== False) {
  $cnt++;
  $pos = strpos($var, $a, $pos+1);
}
echo "There were " .$cnt." A's<br>";
