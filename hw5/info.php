<!DOCTYPE html>
<html>

<head>
  <title>Cell Information</title>
</head>

<body>
  <h2>Hello There!</h2>
  <?php
// Get row and column from GET parameters
$row = isset($_GET['row']) ? $_GET['row'] : '';
$col = isset($_GET['col']) ? $_GET['col'] : '';

if ($row && $col) {
    echo '<p>You clicked on row: <b>' . $row . '</b> and column: <b>' . $col . '</b> to arrive here!</b></p>';
}
?>
  <p><a href="lab1d.php">Back to grid</a></p>
</body>

</html>
