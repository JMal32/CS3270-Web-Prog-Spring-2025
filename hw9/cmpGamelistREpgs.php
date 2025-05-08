<!DOCTYPE html>
<html>

<head>
    <title>Reef Encounter Games - Multiple Pages</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <th>Row #</th>
            <th>Game Name</th>
            <th># of Players</th>
            <th>Player Names</th>
        </tr>
    <?php
    $rowCount = 1;
    $maxPages = 3;
    for ($page = 1; $page <= $maxPages; $page++) {
        $h = file_get_contents('http://www.spielbyweb.com/games.php?list=cmp&page=' . $page);
        $notdone = true;
        while ($notdone) {
            $pbeg = strpos($h, "<TR VALIGN=CENTER");
            $pend = strpos($h, "<TR VALIGN=CENTER", $pbeg+1);
            if ($pbeg === false || $pend === false) {
                $notdone = false;
                break;
            }
            $s = substr($h, $pbeg, $pend - $pbeg);
            if (strpos($s, "Reef Encounter") !== false) {
                // Game name and link
                preg_match('/<A HREF="game.php\\?games_id=(\\d+)[^"]*".*?>(.*?)<\\/A>/i', $s, $gm);
                $gameId = isset($gm[1]) ? $gm[1] : '';
                $gameName = isset($gm[2]) ? $gm[2] : '';
                $gameLink = $gameId ? "http://www.spielbyweb.com/game.php?games_id=$gameId" : '';
                // Player names from onmouseover attribute
                preg_match('/onmouseover="return overlib\(\'(.*?)\', STICKY/i', $s, $overlib);
                $playerNames = [];
                if (isset($overlib[1])) {
                    $tableHtml = $overlib[1];
                    preg_match_all('/>([A-Za-z0-9_]+)<\\/A>/', $tableHtml, $names);
                    $playerNames = $names[1];
                }
                $playerCount = count($playerNames);
                echo "<tr>";
                echo "<td>$rowCount</td>";
                echo "<td><a href='" . htmlspecialchars($gameLink) . "'>" . htmlspecialchars($gameName) . "</a></td>";
                echo "<td>" . htmlspecialchars($playerCount) . "</td>";
                echo "<td>" . htmlspecialchars(implode('', $playerNames)) . "</td>";
                echo "</tr>";
                $rowCount++;
            }
            $h = substr($h, $pend);
        }
    }
    ?>
    </table>
</body>

</html>
