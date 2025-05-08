<?php
////////////////////////////////////////////////////////////////////////
///////////////////////// STATE Class //////////////////////////////////

class REstate {

  public $bord;		// 4-length array of Reef arrays
  public $games_id;		// id for SBW RE game whose state this object maintains
  public $doms;		// 10-length array of dom-tiles?
  public $players;	// 4-length player-names (+ shrimp + cubes?)
  public $polyps;	// 3D array: polyps[PLYR][""][""] -> ["eaten"][CLR], ["inhand"][CLR], ["consumed"][CLR]
  public $cubes;	// 2D array: cubes[PLYR][CLR]
  public $shrimp;	// 2D array: shrimp[PLYR][""] -> ["inhand"],["onboard"],["eaten"]
  public $name_map;	// assoc array of names => player-order ints
  public $shrimp_map;   // numeric array of players' shrimp characters
  public $polyp_map; // numeric array of polyp characters
  
  // Constructor - initializes the state
  function __construct($nms, $gid) { // Changed from initState to standard constructor
    $this->bord = $this->initBord();
    $this->games_id = $gid;
    // Fixed polyp array structure based on comments
    $this->polyps = array_fill(0, 4, array("eaten"=>array_fill(0, 5, 0),
    			"inhand"=>array_fill(0, 5, 0),
    			"consumed"=>array_fill(0, 5, 0)) );  // polyps[PLYR]["eaten"/"inhand"/"consumed"][CLR]
    
    $this->players = $nms;
    // Fixed shrimp array structure based on comments
    $this->shrimp = array_fill(0, 4, array("inhand"=>4,
    			"onboard"=>0,
    			"eaten"=>0) );			// shrimp[PLYR]["inhand"/"onboard"/"eaten"]
    $this->cubes = array_fill(0, 4, array_fill(0, 5, 0));	// cubes[PLYR][CLR]
    
    // Initialize name_map only if names are provided
    $this->name_map = array();
    if (!empty($nms)) {
        $this->name_map = array($nms[0]=>0, $nms[1]=>1, $nms[2]=>2, $nms[3]=>3); // Assumes 4 players
    }
    
    $this->shrimp_map = array(0=>'P', 1=>'G', 2=>'R', 3=>'Y'); // Index is player number
    $this->polyp_map = array(0=>'w', 1=>'y', 2=>'o', 3=>'p', 4=>'g'); // Index is color number
    
    // Initialize Dominance Tiles (placeholder - actual logic TBD)
    $this->doms = array_fill(0, 10, null); 
  }
  
  function initBord() {
  
     // lists contents of each reef-board (includes invalid placement spaces)
     // Added the 20 starting tiles based on instructions
      $rf0 = array(0=>'x','x','x','','','','x','x','','o','y','','','x','','p','x','','w','','','','g','','','','x','','','','p','x','o','','','','','','','','');
      $rf1 = array('x','x','x','','','','x','','w','','g','','','x','','','y','','x','p','','','x','',' ','','w','','','','o','','p','','','x','','','','','','x');
      $rf2 = array('x','','','x','','','','x','y','','','o','','x','g','','w','x','','y','','','',' ','','x','','x','','p','','g','','','x','','','','x','','');
      $rf3 = array(0=>'x','','','','','','x','y','','','o','x','','p','','x','','g','','','','w','','','o','','','x','','p','','y','','x','x','','','x','','','x');
  
      $bord[0] = $rf0;
      $bord[1] = $rf1;
      $bord[2] = $rf2;
      $bord[3] = $rf3;
  
      return $bord;
  }
  
  function clrstr2int($c) {
    // Handle potential empty string or invalid input
    if (empty($c)) return -1; // Or throw an exception
    
    $c = lcfirst(ltrim($c))[0];

    if ($c == "w") {
      $res = 0;
    } else if ($c == 'y') {
      $res = 1;
    } else if ($c == 'o') {
      $res = 2;
    } else if ($c == 'p') {
      $res = 3;
    } else if ($c == 'g') { // Check for green
      $res = 4;
    } else {
      // Handle unexpected color character
      // You might want to log this or return an error indicator
      $res = -1; 
    }

    return $res;
  } //end clrstr2int() method
  

  function cell2slot($cell) {
  /* Given a cell address, determines which slot in a 42-element (0-based) array represents it  */
    // Add input validation
    if (empty($cell) || strlen($cell) < 2) return -1;
    
    $let = lcfirst($cell[0]); 
    $num = (integer)(substr($cell, 1));
    
    // Basic validation for expected cell format (e.g., a1 to n12)
    if ($num < 1 || $num > 12 || ord($let) < 97 || ord($let) > 110) return -1; 
    
    $num--;  // 0-based array for row index
    $let = ord($let) - 97; // 0-based index for column
    
    // Calculate slot based on board layout (7 columns per board section)
    $slot = floor($num / 6) * 21 + ($num % 6) * 7 + floor($let / 7) * 42 + ($let % 7);
    // This calculation needs verification based on the actual board layout 
    // The original calculation seemed incorrect for a 4-board, 42-slot-per-board layout.
    // Let's try a simpler direct calculation assuming 0-6 is board 0/2 cols, 7-13 is board 1/3 cols
    // and 0-5 is board 0/1 rows, 6-11 is board 2/3 rows
    
    $col_index = $let % 7; // 0-6 column within a board quadrant
    $row_index = $num % 6; // 0-5 row within a board quadrant
    
    $slot = $row_index * 7 + $col_index; // Slot within the 42-element board array

    // Add boundary check
    if ($slot < 0 || $slot > 41) return -1;

    return $slot;
  }	//end cell2slot() method
  

  function cell2board($cell) {
  /* Given a cell-address, determines which board-array contains it; outputs INT  */
     // Add input validation
    if (empty($cell) || strlen($cell) < 2) return -1;

    $let = lcfirst($cell[0]); 
    $num = (integer)substr($cell, 1);
    
     // Basic validation for expected cell format (e.g., a1 to n12)
    if ($num < 1 || $num > 12 || ord($let) < 97 || ord($let) > 110) return -1; 

    $let_ord = ord($let);
    
    if ($let_ord < 104) { // a-g (ord 97-103)
      if ($num < 7) { // 1-6
        return 0;
      } else { // 7-12
        return 2;
      }
    } else {  // h-n (ord 104-110)
      if ($num < 7) { // 1-6
        return 1;
      } else { // 7-12
        return 3;
      }
    }
  }	//end cell2board() method
  
}