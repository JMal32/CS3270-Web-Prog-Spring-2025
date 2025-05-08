<?php
include_once("REstate_cls.php");
session_start();

function calculate_scores($state, $playerCount) {
    $scores = array_fill(0, $playerCount, 0);
    if (!$state) return $scores;

    for ($p = 0; $p < $playerCount; $p++) {

        if (isset($state->polyps[$p]['eaten'])) {
            foreach ($state->polyps[$p]['eaten'] as $count) {
                $scores[$p] += $count;
            }
        }

        if (isset($state->shrimp[$p]['eaten'])) {
            $scores[$p] += $state->shrimp[$p]['eaten'];
        }

    }

    return $scores;
}


if (!isset($_GET['gameID'])) {
    header('Location: index.php');
    exit;
}
$gameID = $_GET['gameID'];

if (!isset($_SESSION['game_id']) || $_SESSION['game_id'] != $gameID || !isset($_SESSION['game_states']) || !isset($_SESSION['player_names'])) {

    header("Location: PRElapse.php?gameID=$gameID");
    exit;
}

$game_states = $_SESSION['game_states'];
$player_names = $_SESSION['player_names'];
$playerCount = count($player_names);
$finalState = end($game_states);

if (!$finalState) {
    die("Error: Could not retrieve final game state from session.");
}


$calculated_scores = calculate_scores($finalState, $playerCount);


$official_scores = array_fill(0, $playerCount, 'N/A');
$official_winner = 'N/A';

$log_url = 'http://www.spielbyweb.com/gamelog.php?games_id=' . $gameID;
$html = @file_get_contents($log_url);

if ($html) {
    preg_match_all('/<tr><td align=right><b>(\d+)\.<\/b><\/td>.*?class="player_ref[^>]*>([^<]+)<\/a>.*?<b>Score: ([-+]?\d+)<\/b>/si', $html, $scoreMatches, PREG_SET_ORDER);

    foreach ($scoreMatches as $match) {
        $rank = $match[1];
        $name = trim($match[2]);
        $score = (int)$match[3];

        $playerIndex = array_search($name, $player_names);
        if ($playerIndex !== false) {
            $official_scores[$playerIndex] = $score;
        }
        if ($rank == 1) {
            $official_winner = $name;
        }
    }
}


function get_cell_content($row, $col, $state) {
            $boardIndex = -1;
            $slotIndex = -1;
            $local_row = $row % 6;
            $local_col = $col % 7;
            if ($row < 6) { $boardIndex = ($col < 7) ? 0 : 1; }
            else { $boardIndex = ($col < 7) ? 2 : 3; }
            $slotIndex = $local_row * 7 + $local_col;

            if ($boardIndex !== -1 && $slotIndex !== -1 && isset($state->bord[$boardIndex][$slotIndex])) {
                $content = $state->bord[$boardIndex][$slotIndex];
                 if (in_array($content, $state->shrimp_map)) {
                     return "<span style='color:red; font-weight:bold;'>" . htmlspecialchars($content) . "</span>";
                 } elseif (in_array($content, $state->polyp_map)) {
                     $color_map = ['w' => 'grey', 'y' => 'gold', 'o' => 'orange', 'p' => 'purple', 'g' => 'green'];
                     $css_color = $color_map[$content] ?? 'black';
                     return "<span style='color:$css_color;'>" . htmlspecialchars($content) . "</span>";
                 } elseif ($content === 'x') { return "<span style='color:#ddd;'>x</span>"; }
                 else { return "&nbsp;"; }
            } else { return "&nbsp;"; }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reef Encounter Game #<?php echo $gameID; ?> - Final Results</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: center; }
        th { background-color: #eee; }
        .board-table td { width: 20px; height: 20px; }
        .results-table td { text-align: left; padding-left: 10px; padding-right: 10px; }
        .results-table td:first-child { font-weight: bold; }
        .mismatch { background-color: #FFDDDD; }
    </style>
</head>
<body>

<h1>Reef Encounter Game #<?php echo $gameID; ?> - Final Results</h1>

<h2>Final Board State</h2>
 <table border="1" cellpadding="2" cellspacing="0" class="board-table" style="font-family: monospace;">
        <?php
        for ($r = 0; $r < 12; $r++) {
            echo "<tr>";
            for ($c = 0; $c < 14; $c++) {
                 $cellContent = get_cell_content($r, $c, $finalState);
                 $style = "";
                 if ($c == 6) $style .= " border-right: 2px solid black;";
                 if ($r == 5) $style .= " border-bottom: 2px solid black;";
                 echo "<td style='$style'>" . $cellContent . "</td>";
            }
            echo "</tr>";
        }
        ?>
 </table>

<h2>Score Comparison</h2>
 <table border="1" cellpadding="4" cellspacing="0" class="results-table">
    <tr>
        <th>Player</th>
        <th>Calculated Score (Basic)</th>
        <th>Official SBW Score</th>
    </tr>
    <?php for ($i = 0; $i < $playerCount; $i++):
        $pName = htmlspecialchars($player_names[$i] ?? 'Player ' . ($i+1));
        $calcScore = $calculated_scores[$i];
        $offScore = $official_scores[$i];
        $mismatchClass = ($offScore !== 'N/A' && $calcScore != $offScore) ? ' class="mismatch"' : '';
    ?>
    <tr<?php echo $mismatchClass; ?>>
        <td><?php echo $pName; ?></td>
        <td style="text-align: center;"><?php echo $calcScore; ?></td>
        <td style="text-align: center;"><?php echo $offScore; ?></td>
    </tr>
    <?php endfor; ?>
 </table>
 <p>Official Winner (from SBW): <?php echo htmlspecialchars($official_winner); ?></p>
 <p><strong>Note:</strong> Calculated scores currently only include points from eaten polyps and eaten shrimp. Full scoring logic (largest groups, consumed corals, etc.) is not yet implemented.</p>

 <p><a href="RElapse.php?gameID=<?php echo $gameID; ?>&state_index=<?php echo $_SESSION['current_state_index'] ?? 0; ?>">Back to Last State</a> | <a href="index.php">Back to Game List</a></p>

</body>
</html>
