<?php
$games = array();
$page = 0;
$gamesPerPage = 20;

while (count($games) < 32) {
    $h = file_get_contents('http://www.spielbyweb.com/games.php?list=cmp&page=' . $page);
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
            // Extract game name and link
            preg_match('/<a href="(.*?)">(.*?)<\/a>/', $s, $matches);
            $gameLink = $matches[1];
            $gameName = $matches[2];
            
            // Extract number of players
            preg_match('/<td class="col2">(\d+)<\/td>/', $s, $playerCount);
            $numPlayers = $playerCount[1];
            
            // Extract player names
            preg_match('/<td class="col3">(.*?)<\/td>/', $s, $players);
            $playerNames = $players[1];
            
            $games[] = array(
                'name' => $gameName,
                'link' => $gameLink,
                'players' => $numPlayers,
                'playerNames' => $playerNames
            );
            
            if (count($games) >= 32) break;
        }
        $h = substr($h, $pend);
    }
    
    $page++;
    if ($page > 5) break; // Safety limit
}

echo '<table border="1" cellpadding="5">';
echo '<tr><th>#</th><th>Name</th><th>#p</th><th>Players</th></tr>';

$count = 1;
foreach ($games as $game) {
    echo '<tr>';
    echo '<td>' . $count . '</td>';
    echo '<td><a href="' . htmlspecialchars($game['link']) . '">' . htmlspecialchars($game['name']) . '</a></td>';
    echo '<td>' . htmlspecialchars($game['players']) . '</td>';
    echo '<td>' . htmlspecialchars($game['playerNames']) . '</td>';
    echo '</tr>';
    $count++;
}

echo '</table>';
?> 