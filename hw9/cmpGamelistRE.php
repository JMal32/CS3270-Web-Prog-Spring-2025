<!DOCTYPE html>
<html>
<head>
    <title>Reef Encounter Games List</title>
    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
<?php
$h = file_get_contents('http://www.spielbyweb.com/games.php?list=cmp');
$notdone = TRUE;
while ($notdone) {
    $pbeg = strpos($h, "<TR VALIGN=CENTER");
    $pend = strpos($h, "<TR VALIGN=CENTER", $pbeg+1);
    if ($pend === FALSE) {
        $notdone = FALSE;
        $pend = strpos($h, "Page:");
    }
    $s = substr($h, $pbeg, $pend - $pbeg);
    if (strpos($s, "Reef Encounter") !== FALSE) {
        print "<pre>" . $s . "</pre><hr>";
    }
    $h = substr($h, $pend);
}
?>
</body>
</html> 