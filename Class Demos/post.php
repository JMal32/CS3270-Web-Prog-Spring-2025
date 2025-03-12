<html><head><title></title></head>
<body>

<?php
if (isset($_REQUEST)) {
  echo "<pre>";
  print_r($_REQUEST);
  echo "</pre><br><br>";

  echo "Guess was: " . $_REQUEST['txtGuess'] . "<br>";

}
?>
</body>
</html>
