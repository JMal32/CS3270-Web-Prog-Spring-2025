<?php
$servername = 'localhost';
$username = 'jmalone';
$password = 'pz0348yv';
$dbname = 'jmalone';

$sqlLogin = new mysqli($servername, $username, $password, $dbname);

if ($sqlLogin->connect_error) {
    die('Failed: ' . $sqlLogin->connect_error);
} else {
    echo 'Connect to jmalone successfully<br>' . PHP_EOL;
}
$sql = 'SELECT * FROM professors';
$result = $sqlLogin->query($sql);
if ($result) {  // check if the query is successful
    // Fetch a row
    $row = $result->fetch_row();
    // Print the row
    echo '<pre>';
    print_r($row);
    echo '</pre>';
    // Print a specific element from the row
    echo $row[1] . '<br>';
}
