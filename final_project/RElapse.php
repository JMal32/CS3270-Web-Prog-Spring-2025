<?php

include_once("REstate_cls.php");
session_start();

if (
    !isset($_SESSION['game_id']) ||
    !isset($_SESSION['game_states']) ||
    !isset($_SESSION['current_state_index']) ||
    !isset($_SESSION['player_names'])
) {
    header('Location: index.php');
    exit;
}

$gameID = $_SESSION['game_id'];
$state_index = $_SESSION['current_state_index'];
$game_states = $_SESSION['game_states'];
$player_names = $_SESSION['player_names'];
$player_count = count($player_names);
$max_state_index = count($game_states) - 1;

if ($state_index < 0 || $state_index > $max_state_index || !isset($game_states[$state_index])) {
    $state_index = 0;
    $_SESSION['current_state_index'] = 0;
}

$current_state = $game_states[$state_index];

$prev_state_index = $state_index - 1;
$next_state_index = $state_index + 1;

$is_initial_state = ($state_index == 0);
$is_final_state = ($state_index == $max_state_index);

// Handle navigation
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'prev':
            if (!$is_initial_state) {
                $_SESSION['current_state_index'] = $prev_state_index;
                header("Location: RElapse.php");
                exit;
            }
            break;
        case 'next':
            if (!$is_final_state) {
                $_SESSION['current_state_index'] = $next_state_index;
                header("Location: RElapse.php");
                exit;
            }
            break;
        case 'first':
            $_SESSION['current_state_index'] = 0;
            header("Location: RElapse.php");
            exit;
            break;
        case 'last':
            $_SESSION['current_state_index'] = $max_state_index;
            header("Location: RElapse.php");
            exit;
            break;
    }
}

// If this is the final state, redirect to REfinal.php
if ($is_final_state && isset($_GET['action']) && $_GET['action'] === 'next') {
    header("Location: REfinal.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Reef Encounter Game #<?php echo $gameID; ?> - State <?php echo $state_index; ?></title>
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

    h1,
    h2,
    h3 {
      color: #333;
      margin-bottom: 15px;
    }

    .game-board {
      margin-bottom: 20px;
      overflow-x: auto;
    }

    .player-info {
      margin-bottom: 20px;
    }

    .navigation {
      margin: 20px 0;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 5px;
      text-align: center;
    }

    .navigation a {
      display: inline-block;
      margin: 0 5px;
      padding: 8px 15px;
      text-decoration: none;
      color: #0066cc;
      background-color: #e9ecef;
      border-radius: 4px;
      transition: background-color 0.2s;
    }

    .navigation a:hover {
      background-color: #dee2e6;
    }

    .navigation a.disabled {
      color: #6c757d;
      pointer-events: none;
      background-color: #e9ecef;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin: 10px 0;
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

    .board-cell {
      width: 30px;
      height: 30px;
      text-align: center;
      vertical-align: middle;
      font-weight: bold;
    }

    .board-divider {
      border: 2px solid #666;
    }

    .resource-count {
      font-weight: bold;
      margin-right: 5px;
    }

    .color-dot {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      margin-right: 3px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Reef Encounter Game #<?php echo $gameID; ?></h1>
    <h2>Game State <?php echo $state_index; ?> of <?php echo $max_state_index; ?></h2>

    <div class="navigation">
      <a href="?action=first" <?php echo $is_initial_state ? 'class="disabled"' : ''; ?>>First</a>
      <a href="?action=prev" <?php echo $is_initial_state ? 'class="disabled"' : ''; ?>>Previous</a>
      <a href="?action=next" <?php echo $is_final_state ? 'class="disabled"' : ''; ?>>Next</a>
      <a href="?action=last" <?php echo $is_final_state ? 'class="disabled"' : ''; ?>>Last</a>
    </div>

    <div class="game-board">
      <h3>Game Board</h3>

      <table border="1" cellpadding="2" cellspacing="0">
          <?php
          function get_cell_content($row, $col, $state) {
              $board_index = -1;
              $slot_index = -1;

              $local_row = $row % 6;
              $local_col = $col % 7;

              if ($row < 6) {
                  $board_index = ($col < 7) ? 0 : 1;
              } else {
                  $board_index = ($col < 7) ? 2 : 3;
              }

              $slot_index = $local_row * 7 + $local_col;

              if ($board_index !== -1 && $slot_index !== -1 && isset($state->bord[$board_index][$slot_index])) {
                  $content = $state->bord[$board_index][$slot_index];
                   if (in_array($content, $state->shrimp_map)) {
                       return "<span style='color:red; font-weight:bold;'>" . htmlspecialchars($content) . "</span>";
                   } elseif (in_array($content, $state->polyp_map)) {
                       $color_map = ['w' => 'grey', 'y' => 'gold', 'o' => 'orange', 'p' => 'purple', 'g' => 'green'];
                       $css_color = $color_map[$content] ?? 'black';
                       return "<span style='color:$css_color;'>" . htmlspecialchars($content) . "</span>";
                   } elseif ($content === 'x') {
                       return "<span style='color:#ddd;'>x</span>";
                   } else {
                       return "&nbsp;";
                   }
              } else {
                  return "&nbsp;";
              }
          }

          for ($r = 0; $r < 12; $r++) {
              echo "<tr>";
              for ($c = 0; $c < 14; $c++) {
                   $cell_content = get_cell_content($r, $c, $current_state);
                   $style = "width: 30px; height: 30px; text-align: center;";
                   if ($c == 6) $style .= " border-right: 2px solid #666;";
                   if ($r == 5) $style .= " border-bottom: 2px solid #666;";
                   
                   echo "<td class='board-cell' style='$style'>" . $cell_content . "</td>";
              }
              echo "</tr>";
          }
          ?>
      </table>
      
      <h3>Player Resources</h3>
       <table border="1" cellpadding="4" cellspacing="0">
        <tr bgcolor="#f5f5f5">
          <th>Player</th>
          <th>Larvae Cubes</th>
          <th>Polyp Tiles (In Hand)</th>
          <th>Eaten Polyps</th>
          <th>Shrimp (H/B/E)</th>
        </tr>
        <?php
        $colors = ['w' => 'grey', 'y' => 'gold', 'o' => 'orange', 'p' => 'purple', 'g' => 'green'];
        for ($i = 0; $i < $player_count; $i++): 
          $p_name = htmlspecialchars($player_names[$i] ?? 'Player ' . ($i+1));
        ?>
          <tr>
            <td><b><?php echo $p_name; ?></b></td>
            <td>
              <?php
              $cube_strs = [];
              foreach ($colors as $char => $css_color) {
                   $color_idx = $current_state->clrstr2int($char);
                   if ($color_idx !== -1) {
                       $count = $current_state->cubes[$i][$color_idx] ?? 0;
                       if ($count > 0) {
                           $cube_strs[] = "<span style='color:$css_color'>●</span> $count"; 
                       }
                   } 
              }
              echo empty($cube_strs) ? '0' : implode(", ", $cube_strs);
              ?>
            </td>
            <td>
              <?php
              $tile_strs = [];
              foreach ($colors as $char => $css_color) {
                   $color_idx = $current_state->clrstr2int($char);
                   if ($color_idx !== -1) {
                       $count = $current_state->polyps[$i]['inhand'][$color_idx] ?? 0;
                       if ($count > 0) {
                           $tile_strs[] = "<span style='color:$css_color'>■</span> $count"; 
                       }
                   } 
              }
              echo empty($tile_strs) ? '0' : implode(", ", $tile_strs);
              ?>
            </td>
             <td>
              <?php
              $eaten_strs = [];
              foreach ($colors as $char => $css_color) {
                   $color_idx = $current_state->clrstr2int($char);
                   if ($color_idx !== -1) {
                       $count = $current_state->polyps[$i]['eaten'][$color_idx] ?? 0;
                       if ($count > 0) {
                           $eaten_strs[] = "<span style='color:$css_color'>■</span> $count"; 
                       }
                   } 
              }
              echo empty($eaten_strs) ? '0' : implode(", ", $eaten_strs);
              ?>
            </td>
             <td>
              <?php
               $shrimp = $current_state->shrimp[$i];
               echo "{$shrimp['inhand']}/{$shrimp['onboard']}/{$shrimp['eaten']}";
              ?>
            </td>
          </tr>
        <?php endfor; ?>
      </table>
     
    </div>

    <?php if ($is_final_state): ?>
      <div class="navigation">
        <a href="REfinal.php">View Final Results</a>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>
