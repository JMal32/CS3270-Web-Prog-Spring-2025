<?php
$count = $_COOKIE['visits'];
if ($count == '') {
    $count = 1;
} else {
    $count++;
}
setcookie('visits', $count);
?>

<html>

<head>
  <title>Cookie Stuff</title>
</head>

<body>
  <font face="verdana" size="+1">
    <h2>Visitor Count with Cookies</h2>
    You are visitor number <?= $count ?> <br>
  </font>
</body>

</html>
