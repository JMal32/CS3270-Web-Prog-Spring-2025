
<html>
<head>
    <title>PHP Test</title></head>
  <body>
    <h1>Array Testing</h1><br />
  <?php 
    $ary1 = array(1 => "R1C1","R1C2");
    $ary2 = array(1 => "R2C1","R2C2");
    $ary = array(65 => $ary1, 66 => $ary2);

    echo '<pre>';
    print_r($ary);
    echo '</pre>';
    for ($r=65; $r <= 66; $r++) {
      for ($c = 1; $c <= 2; $c++) {
        echo $ary[$r][$c] . " ";
      }
      echo "<br>";
    }
    echo "<br><br><br>";
    echo "The number of elements in \$ary: " . count($ary) . "\n<br>";
    echo "The rec-total of elements: " , count($ary, COUNT_RECURSIVE), "\n<br>";
  
  
  
  ?>
</body>
</html>
