<!Doctype html>
<html>
  <head>
    <title>Lab 1b</title>
  </head>
  <body>
    <form method="post">
      Enter number of columns (2-20): 
      <input type="number" name="cols" min="2" max="20" value="12">
      <input type="submit" value="Submit">
    </form>
<?php
    
    $aryc = array(1 => "");
    $ary = array(65 => $aryc);
    $colno = 16;
    if (isset($_POST["colno"])) {
      $colno = $_POST["colno"];
    }

    $ordletstart = ord("A");
    $ordletend = ord("K");

    for ($r = $ordletstart; $r <= $ordletend; $r++) {
      for ($c = 1; $c <= $colno; $c++) {
        $ary[$r][$c] = chr($r) . "-" . $c;
      }
}

    echo '<pre>';
    for ($r = $ordletstart; $r <= $ordletend; $r++) {
      for ($c = 1; $c <= $colno; $c++) {
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
