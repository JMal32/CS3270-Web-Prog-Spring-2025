<?php
$count = $_COOKIE['visits'];
if ($count == '') {
    $count = 1;
} else {
    $count++;
    setcookie('visits', $count);
}
?>

<html>

<head>
  <title>Cookie Stuff</title>
</head>

<body>
  <font face="verdana" size="+1">
    <h2>Visitor Count with Cookies</h2>
    You are visitor number <?php echo $count; ?> <br>
  </font>
</body>

</html>

<?php
setcookie('usr', 'static_');
setcookie('color', 'blue');
?>

<html>

<head>
  <title>Cookie Stuff</title>
</head>

<body>
  <font face="verdana" size="+1">
    <h2> $_COOKIE[]</h2>

    <?php
    // Display cookie values
    if (!empty($_COOKIE['color'])) {
        echo '<pre>';
        print_r($_COOKIE);
        echo '<pre>';
    }

    ?>
  </font>
</body>

</html>
