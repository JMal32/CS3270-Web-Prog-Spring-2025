<?php
session_start();
?>
<html>

<head>
  <title>Session Stuff</title>
</head>

<body>
  <?php
// Set session variables
$_SESSION['favcolor'] = 'green';
$_SESSION['favanimal'] = 'cat';
echo 'Session variables are set. <br><br>';
?>






</body>

</html>
