
<?php
$ary = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

for ($r = 0; $r < count($ary); $r++) {
    for ($c = 0; $c < count($ary[$r]); $c++) {
        echo $ary[$r][$c] . " ";
    }
    echo "<br>";
}
?>
