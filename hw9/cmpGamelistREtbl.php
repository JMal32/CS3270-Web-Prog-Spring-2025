<?php
$h = file_get_contents('http://www.spielbyweb.com/games.php?list=cmp');
$notdone = TRUE;

echo '<table border="1" cellpadding="5">';
echo '<tr><th>Game Name</th><th># of Players</th><th>Player Names</th></tr>';

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
        
        echo '<tr>';
        echo '<td><a href="' . htmlspecialchars($gameLink) . '">' . htmlspecialchars($gameName) . '</a></td>';
        echo '<td>' . htmlspecialchars($numPlayers) . '</td>';
        echo '<td>' . htmlspecialchars($playerNames) . '</td>';
        echo '</tr>';
    }
    $h = substr($h, $pend);
}

echo '</table>';
?> 