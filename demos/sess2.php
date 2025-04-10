<?php
session_start();
?>
<html>

<head>
  <title>Session Stuff 2</title>
</head>

<body>
  <?php
// REtrieve session variables
echo 'Favorite color is ' . $_SESSION['favcolor'] . '.<br>';
echo 'Favorte animal is ' . $_SESSION['favanimal'] . '.<br><br>';
echo '<br><br><pre>' . print_r($_SESSION) . '</pre>';
?>
</body>

</html>
