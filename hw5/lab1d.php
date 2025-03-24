<!Doctype html>
<html>

<head>
  <title>Lab 1d</title>
  <style>
    body {
      position: relative;
      padding-top: 50px;
      padding-left: 50px;
    }

    .cell {
      text-align: center;
      width: 40px;
      height: 40px;
      position: absolute;
      border: 1px solid #ddd;
      line-height: 40px;
    }

    .header {
      font-weight: bold;
      background-color: #f0f0f0;
    }
  </style>
</head>

<body>
  <h2>Automated 2D Array Using CSS Positioning</h2>
  <?php
// Fixed settings
$colno = 20;
$ordletstart = ord('A');
$ordletend = ord('K');

// Base position for the grid
$baseLeft = 50;
$baseTop = 100;

$aryc = array(1 => '');
$ary = array(65 => $aryc);

for ($r = $ordletstart; $r <= $ordletend; $r++) {
    for ($c = 1; $c <= $colno; $c++) {
        $ary[$r][$c] = chr($r) . '-' . $c;
    }
}

// This is where I implemented the table with the CSS Positioning
// Columns - top
for ($c = 1; $c <= $colno; $c++) {
    $left = $baseLeft + ($c * 40);
    echo "<span class='cell header' style='left:" . $left . 'px; top:' . $baseTop . "px;'>" . $c . '</span>';
}

// Rows - top
for ($r = $ordletstart; $r <= $ordletend; $r++) {
    $rowIndex = $r - $ordletstart + 1;
    $top = $baseTop + ($rowIndex * 40);
    echo "<span class='cell header' style='left:" . $baseLeft . 'px; top:' . $top . "px;'>" . chr($r) . '</span>';
}
// Columns - bottom
for ($c = 1; $c <= $colno; $c++) {
    $left = $baseLeft + ($c * 40);
    $top = $baseTop + (($ordletend - $ordletstart + 2) * 40);
    echo "<span class='cell header' style='left:" . $left . 'px; top:' . $top . "px;'>" . $c . '</span>';
}

// Rows - bottom
for ($r = $ordletstart; $r <= $ordletend; $r++) {
    $rowIndex = $r - $ordletstart + 1;
    $left = $baseLeft + (($colno + 1) * 40);
    $top = $baseTop + ($rowIndex * 40);
    echo "<span class='cell header' style='left:" . $left . 'px; top:' . $top . "px;'>" . chr($r) . '</span>';
}

// Cells
for ($r = $ordletstart; $r <= $ordletend; $r++) {
    $rowIndex = $r - $ordletstart + 1;

    for ($c = 1; $c <= $colno; $c++) {
        $left = $baseLeft + ($c * 40);
        $top = $baseTop + ($rowIndex * 40);

        // Make each cell a link to info.php with row and column parameters
        echo "<span class='cell' style='left:" . $left . 'px; top:' . $top . "px;'>";
        echo "<a href='info.php?row=" . chr($r) . '&col=' . $c . "'>" . $ary[$r][$c] . '</a>';
        echo '</span>';
    }
}
?>
</body>

</html>
