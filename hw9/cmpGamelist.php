<!DOCTYPE html>
<html>

<head>
    <title>Completed Games List</title>
    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <?php
    $h = file_get_contents('http://www.spielbyweb.com/games.php?
list=cmp');
    $notdone = true;
    while ($notdone) {
        $pbeg = strpos($h, "<TR VALIGN=CENTER");
        $pend = strpos($h, "<TR VALIGN=CENTER", $pbeg+1);
        if ($pbeg === false || $pend === false) {
            $notdone = false;
            break;
        }
        $s = substr($h, $pbeg, $pend - $pbeg);
        print "<b><font face='courier'>" . htmlentities($s) . "</font></b><br><br>";
        $h = substr($h, $pend);
    }
    echo $h;
    ?>
</body>

</html>
