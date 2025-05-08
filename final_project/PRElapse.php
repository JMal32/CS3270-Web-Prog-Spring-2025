<?php

include_once("REstate_cls.php");

session_start();

if (!isset($_GET['gameID'])) {
    header('Location: index.php');
    exit;
}

$gameID = $_GET['gameID'];
$base_log_url = 'http://www.spielbyweb.com/gamelog.php?games_id=';
$url = $base_log_url . $gameID;

$context = stream_context_create([
    'http' => [
        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ]
]);

$html = @file_get_contents($url, false, $context);

if ($html === false) {
    die('Error: Could not retrieve game log from SpielByWeb. Please try again later.');
}

$playerNames = array();
preg_match_all('/<tr[^>]*>.*?<span[^>]+class="player-name"[^>]*>([^<]+)<\/span>\s*had picked.*?larva cube.*?<\/td>/si', $html, $nameMatches);

if (isset($nameMatches[1]) && count($nameMatches[1]) >= 2) {
    $playerNames = array_map('trim', $nameMatches[1]);
    $playerNames = array_reverse($playerNames);
} else {
    preg_match_all('/<tr[^>]*>.*?<a[^>]+class="player-link"[^>]*>([^<]+)<\/a>\s*had picked.*?larva cube.*?<\/td>/si', $html, $nameMatches);
    if (isset($nameMatches[1]) && count($nameMatches[1]) >= 2) {
        $playerNames = array_map('trim', $nameMatches[1]);
        $playerNames = array_reverse($playerNames);
    } else {
        die('Error: Could not extract player names from the game log.');
    }
}

if (empty($playerNames)) {
    die('Error: Could not reliably extract player names from the game log.');
}
$playerCount = count($playerNames);

function eatPolyps($location, $targetColor, &$state) {
  $boardIndex = $state->cell2board($location);
  $slotIndex = $state->cell2slot($location);

  if ($boardIndex === -1 || $slotIndex === -1 || empty($targetColor)) {
      return 0;
  }

  $polypColorIndex = $state->clrstr2int($targetColor);
  if ($polypColorIndex === -1) return 0;

  $queue = array();
  $visited = array();
  $eatenCount = 0;

  $neighbors = array();
  if ($slotIndex % 7 > 0) $neighbors[] = $slotIndex - 1;
  if ($slotIndex % 7 < 6) $neighbors[] = $slotIndex + 1;
  if ($slotIndex >= 7) $neighbors[] = $slotIndex - 7;
  if ($slotIndex < 35) $neighbors[] = $slotIndex + 7;

  foreach ($neighbors as $neighborSlot) {
      if (isset($state->bord[$boardIndex][$neighborSlot]) &&
          $state->bord[$boardIndex][$neighborSlot] === $targetColor)
      {
          if (!isset($visited[$neighborSlot])) {
              $visited[$neighborSlot] = true;
              $queue[] = $neighborSlot;
          }
      }
  }

  $head = 0;
  while ($head < count($queue)) {
    $currentSlot = $queue[$head++];

    if (isset($state->bord[$boardIndex][$currentSlot]) && $state->bord[$boardIndex][$currentSlot] === $targetColor) {
        $state->bord[$boardIndex][$currentSlot] = '';
        $eatenCount++;

        $polyp_neighbors = array();
        if ($currentSlot % 7 > 0) $polyp_neighbors[] = $currentSlot - 1;
        if ($currentSlot % 7 < 6) $polyp_neighbors[] = $currentSlot + 1;
        if ($currentSlot >= 7) $polyp_neighbors[] = $currentSlot - 7;
        if ($currentSlot < 35) $polyp_neighbors[] = $currentSlot + 7;

        foreach ($polyp_neighbors as $neighborSlot) {
            if (!isset($visited[$neighborSlot]) &&
                isset($state->bord[$boardIndex][$neighborSlot]) &&
                $state->bord[$boardIndex][$neighborSlot] === $targetColor)
            {
                $visited[$neighborSlot] = true;
                $queue[] = $neighborSlot;
            }
        }
    }

  }

  return $eatenCount;
}

$currentState = new REstate($playerNames, $gameID);
$states = array();
$turn_actions = array();

$actionBlocks = array();
preg_match_all('/<tr[^>]*class="action-row"[^>]*>.*?<td[^>]*>(.*?)<\/td>.*?<\/tr>/si', $html, $matches);

if (isset($matches[1]) && count($matches[1]) > 0) {
    $actionBlocks = array_reverse($matches[1]);
} else {
    preg_match_all('/<tr[^>]*>.*?<td[^>]*class="action-text"[^>]*>(.*?)<\/td>.*?<\/tr>/si', $html, $matches);
    if (isset($matches[1]) && count($matches[1]) > 0) {
        $actionBlocks = array_reverse($matches[1]);
    } else {
        die("Error: Could not find action blocks in the game log.");
    }
}

array_push($states, clone $currentState);

foreach ($actionBlocks as $block_index => $block_content) {
    $action_player_index = -1;
    $action_player_name = null;
    foreach ($currentState->name_map as $name => $index) {
        if (preg_match('/\b' . preg_quote($name, '/') . '\b/i', $block_content)) {
            $action_player_index = $index;
            $action_player_name = $name;
            break;
        }
    }

    if (strpos($block_content, 'chose initial cube') !== false && $action_player_index !== -1) {
         if (preg_match('/cube: <span[^>]+class="color-(\w+)"[^>]*>/i', $block_content, $colorMatch)) {
             $colorStr = $colorMatch[1];
             $colorIndex = $currentState->clrstr2int($colorStr);
             if ($colorIndex !== -1) {
                $currentState->cubes[$action_player_index][$colorIndex]++;
             }
         }
    }
    else if (strpos($block_content, 'chose action 1') !== false && $action_player_index !== -1) {
         if (preg_match('/\bat ([A-N][1-9][0-2]?)\b/i', $block_content, $locMatch)) {
            $location = strtoupper($locMatch[1]);
            $boardIndex = $currentState->cell2board($location);
            $slotIndex = $currentState->cell2slot($location);

            if ($boardIndex !== -1 && $slotIndex !== -1 && isset($currentState->bord[$boardIndex][$slotIndex])) {
                $shrimpChar = $currentState->shrimp_map[$action_player_index];
                $tileContent = $currentState->bord[$boardIndex][$slotIndex];
                $isShrimp = ($tileContent === $shrimpChar);
                $polypColor = '';
                $polypColorIndex = -1;

                if ($isShrimp) {
                    if (preg_match('/eats <span[^>]+class="color-(\w+)"[^>]*>coral/i', $block_content, $eatColorMatch)) {
                        $polypColor = $eatColorMatch[1][0];
                        $polypColorIndex = $currentState->clrstr2int($polypColor);
                    }
                    if ($polypColorIndex !== -1) {
                       $currentState->bord[$boardIndex][$slotIndex] = '';
                       $currentState->shrimp[$action_player_index]['onboard']--;
                       $currentState->shrimp[$action_player_index]['eaten']++;
                       $numEaten = eatPolyps($location, $polypColor, $currentState);
                       $currentState->polyps[$action_player_index]['eaten'][$polypColorIndex] += $numEaten;
                    }

                } else { 
                    $polypColor = $tileContent; 
                    $polypColorIndex = $currentState->clrstr2int($polypColor);
                    if($polypColorIndex !== -1){ 
                        $currentState->polyps[$action_player_index]['eaten'][$polypColorIndex]++; 
                        $currentState->bord[$boardIndex][$slotIndex] = '';
                    }
                }
            }
        }
    }
    else if (strpos($block_content, 'chose action 2') !== false || strpos($block_content, 'chose action 3') !== false) {
         if (preg_match('/took a <font color=(\w+)>(\w+)<\/font> larva cube/i', $block_content, $cubeMatch)) {
             $colorStr = $cubeMatch[1];
             $colorIndex = $currentState->clrstr2int($colorStr);
             if ($colorIndex !== -1 && $action_player_index !== -1) {
                 $currentState->cubes[$action_player_index][$colorIndex]++;
             }
         }
    }
    else if (strpos($block_content, 'chose action 4') !== false) {
         if (preg_match('/took the <font color=(\w+)>(\w+)<\/font> polyp tile/i', $block_content, $polypMatch)) {
             $colorStr = $polypMatch[1];
             $colorIndex = $currentState->clrstr2int($colorStr);
             if ($colorIndex !== -1 && $action_player_index !== -1) {
                 $currentState->polyps[$action_player_index]['inhand'][$colorIndex]++;
             }
         }
    }
    else if (strpos($block_content, 'chose action 5') !== false) {
         if (preg_match('/at ([A-N][1-9][0-2]?)\b.*places <font color=(\w+)>(\w+)<\/font> polyp/i', $block_content, $placeMatch)) {
             $location = strtoupper($placeMatch[1]);
             $colorStr = $placeMatch[2];
             $colorIndex = $currentState->clrstr2int($colorStr);
             $boardIndex = $currentState->cell2board($location);
             $slotIndex = $currentState->cell2slot($location);

             if ($boardIndex !== -1 && $slotIndex !== -1 && $colorIndex !== -1 && $action_player_index !== -1) {
                 $currentState->bord[$boardIndex][$slotIndex] = $currentState->polyp_map[$colorIndex];
                 $currentState->polyps[$action_player_index]['inhand'][$colorIndex]--; 
                 $currentState->cubes[$action_player_index][$colorIndex]--; 
             }
         }
    }
     else if (strpos($block_content, 'chose action 7') !== false && $action_player_index !== -1) {
        if (preg_match('/places.*?shrimp at ([A-N][1-9][0-2]?)\b/i', $block_content, $shrimpMatch)) {
            $location = strtoupper($shrimpMatch[1]);
            $boardIndex = $currentState->cell2board($location);
            $slotIndex = $currentState->cell2slot($location);
            if ($boardIndex !== -1 && $slotIndex !== -1) {
                $currentState->bord[$boardIndex][$slotIndex] = $currentState->shrimp_map[$action_player_index];
                $currentState->shrimp[$action_player_index]['inhand']--;
                $currentState->shrimp[$action_player_index]['onboard']++;
            }
        }
        // TODO: Need to parse Algae/Coral consumption if any
        // Example: if (preg_match('/consumed: <font color=.../', $block_content, $consumedMatch))...
    }
    else if (strpos($block_content, 'chose action 8') !== false) {
        if (preg_match('/traded in:.*?(\d+) <font color=(\w+)>(\w+)<\/font> polyp tiles/i', $block_content, $tradeMatch)) {
            $count = (int)$tradeMatch[1];
            $colorStr = $tradeMatch[2];
            $colorIndex = $currentState->clrstr2int($colorStr);
            if ($colorIndex !== -1 && $action_player_index !== -1) {
                $currentState->polyps[$action_player_index]['inhand'][$colorIndex] -= $count;
                $currentState->shrimp[$action_player_index]['inhand'] += floor($count / 2);
            }
        }
    }
    else if (strpos($block_content, 'chose action 10') !== false) {
        if (preg_match('/scores (\d+) points/i', $block_content, $scoreMatch)) {
            // Note: We are reconstructing state, not calculating scores here.
            // Scoring happens at the end or isn't tracked turn-by-turn this way.
            // However, Action 10 *consumes* tiles.
            // Need to parse which tiles were consumed from the log text.
            // Example: preg_match_all('/<font color=(\w+)>(\w+)<\/font> polyp tile/i', $block_content, $consumedTiles)
            // Then loop through $consumedTiles and update $currentState->polyps[$player]['inhand']--
            // And maybe $currentState->consumed[]++
        }
    }

    if (strpos($block_content, 'ends their turn.') !== false && $action_player_index !== -1) {
        array_push($states, clone $currentState);
        $turn_actions = array(); 
    }
}

if (empty($states)) {
    die("Error: No game states were generated. Log processing likely failed.");
}

// Save the states array to session
$_SESSION['game_states'] = $states;
$_SESSION['current_state_index'] = 0;
$_SESSION['game_id'] = $gameID;
$_SESSION['player_names'] = $playerNames;

// Redirect to RElapse.php
header("Location: RElapse.php");
exit;
