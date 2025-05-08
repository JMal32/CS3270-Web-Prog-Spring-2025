<?php
session_start();

function fetch_re_games($threshold)
{
    $all_game_entries = array();
    $page = 0;
    $game_count = 0;

    while ($game_count < $threshold && $page < 20) {
        $url = "http://www.spielbyweb.com/games.php?list=cmp";
        if ($page > 0) {
            $url .= "&page=" . ($page + 1);
        }
        
        $context = stream_context_create([
            'http' => [
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
        
        $h = @file_get_contents($url, false, $context);
        if ($h === false) {
            error_log("Failed to fetch games page: " . $url);
            break;
        }

        // Find all game rows
        $pattern = '/<TR[^>]*>.*?<TD[^>]*>.*?<A[^>]*HREF="game\.php\?games_id=(\d+)"[^>]*>(.*?)<\/A>.*?<TD[^>]*ALIGN=CENTER[^>]*ROWSPAN=2[^>]*>.*?(\d+)<\/A>.*?onmouseover="return overlib\(\'(.*?)\'\)".*?<\/TR>/is';
        preg_match_all($pattern, $h, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $game_id = $match[1];
            $game_title = trim(strip_tags($match[2]));
            $player_count = intval($match[3]);
            $tooltip_html = html_entity_decode($match[4]);
            
            // Extract player names from tooltip
            $player_names = [];
            preg_match_all('/>([^<]+)<\/A>/', $tooltip_html, $name_matches);
            if (isset($name_matches[1])) {
                $player_names = array_map('trim', $name_matches[1]);
            }

            // Only include Reef Encounter games with 2-4 players
            if (stripos($game_title, 'Reef Encounter') !== false && $player_count >= 2 && $player_count <= 4) {
                $all_game_entries[] = array(
                    'id' => $game_id,
                    'title' => $game_title,
                    'count' => $player_count,
                    'players' => $player_names
                );
                $game_count++;
                if ($game_count >= $threshold) break;
            }
        }
        $page++;
    }
    return $all_game_entries;
}

$THRESHOLD = 20;
$game_list_to_display = fetch_re_games($THRESHOLD);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Completed RE Games</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .action-link {
            text-decoration: none;
            color: #0066cc;
            padding: 5px 10px;
            border-radius: 3px;
            background-color: #f0f0f0;
        }
        .action-link:hover {
            background-color: #e0e0e0;
        }
        .player-count {
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Completed Reef Encounter Games</h1>

        <?php if (empty($game_list_to_display)): ?>
            <p>No Reef Encounter games found within the first <?php echo $THRESHOLD * 20; ?> completed games scanned, or an error occurred.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Game ID</th>
                    <th>Title</th>
                    <th>Players</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($game_list_to_display as $game): ?>
                    <tr>
                        <td><?php echo $game['id']; ?></td>
                        <td><?php echo htmlspecialchars($game['title']); ?></td>
                        <td class="player-count">
                            <?php echo $game['count']; ?> Players<br>
                            <small><?php echo htmlspecialchars(implode(' vs ', $game['players'])); ?></small>
                        </td>
                        <td><a href="PRElapse.php?gameID=<?php echo $game['id']; ?>" class="action-link">Process & View</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
