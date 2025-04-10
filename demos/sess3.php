<?php
session_start();
?>
<html>

<head>
  <title>Session Stuff</title>
</head>

<body>
  <?php
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
// remove all session variables
session_unset();
// destroy session
session_destroy();
echo 'Session destroyed.';
?>
  Click <a href="sess2.php">here</a> to return back to session 2 page.

</body>

</html
