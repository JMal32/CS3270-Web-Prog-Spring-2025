

<html>
<head>
    <title>PHP Test</title></head>
  <body>
    <h1>Array Testing</h1><br />
<?php
    $aryc = array(1 => "");
    $ary = array(65 => $aryc);
    $colno = 2;

    $ordletstart = ord("A");
    $ordletend = ord("B");

    for ($r = $ordletstart; $r <= $ordletend; $r++) {
      for ($c = 1; $c <= $colno; $c++) {
        $ary[$r][$c] = chr($r) . "-" . $c;
      }
}

    echo '<pre>';
    for ($r = $ordletstart; $r <= $ordletend; r++) {
      for ($c = 1; $c <= 2; $c++) {
        echo $ary[$r][$c] .  " ";
      }
      echo "<br>";
    }
    echo '</pre>';

    echo "<br><br><br>";
    echo "The number of elements in \$ary: " . count($ary) . "\n<br>";
    echo "The rec-total of elements: " , count($ary, COUNT_RECURSIVE), "\n<br>";
  
  
  
  ?>
</body>
</html>
