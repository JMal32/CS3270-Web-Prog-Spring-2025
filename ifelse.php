<html>
<head>
    <title>Form Input Test</title>
</head>
<body>

<?php
echo "<pre>";
print_r($_POST); // Fixed: Changed $POST to $_POST
echo "</pre><br><br>";

if (empty($_POST)) {
    echo "No form sent!<br>";
} else { // Fixed: Removed invalid "else if ()"
    $var = $_POST['txtInput'];

    if (is_null($var)) {
        echo "Var is null!<br>";
    } else {
        if (is_numeric($var)) { // Fixed: Check for numeric instead of using is_string()
            echo "Var is numeric!<br>";
        } else if (is_string($var)) {
            echo "Var is a string!<br>";
        } else {
            echo "Var is something else!<br>"; // Fixed: Moved else inside the correct block
        }
    }
}
?>

<p>Enter an input, and click "Submit"</p>
<form action="ifelse.php" method="POST" name="form1">
    <input type="text" name="txtInput" value="" />
    <input type="submit" name="btnGuess" />
</form>

</body>
</html>
