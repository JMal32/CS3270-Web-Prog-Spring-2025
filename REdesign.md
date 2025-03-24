# Reef Encounter Web Implementation - Design Document

## 1. Introduction

This design document outlines the approach for developing a web-based implementation of the board game Reef Encounter using PHP. Reef Encounter is a strategy game designed by Richard Breese, where players compete to build and grow coral reefs, while strategically consuming other corals using shrimp.

The web implementation will enable online play through a browser interface, allowing for both local and potentially remote multiplayer experiences. This document covers the game's core mechanics, technical implementation considerations, and recommendations for development.

### 1.1 Game Overview

Reef Encounter simulates the growth, competition, and consumption within coral reef ecosystems. Players:
- Grow different types of coral colonies
- Compete for dominance between coral types
- Use shrimp to protect and consume coral
- Score points by feeding coral to their parrotfish

The game combines area control, tile placement, and resource management mechanics with a unique biological theme. The player with the most valuable coral consumed by their parrotfish at game end is the winner.

## 2. Game Rules Summary

### 2.1 Components to Implement

- **Coral Tiles**: 5 colors/types (red, green, blue, yellow, purple)
- **Coral Reef Boards**: Player-specific boards with grid spaces
- **Larva Cubes**: Small cubes representing coral larvae (matching the 5 coral colors)
- **Polyp Tiles**: Used to adjust relative coral strengths
- **Shrimp Tokens**: Used to protect coral and consume opponent's coral
- **Parrotfish**: One per player, used to store consumed coral tiles
- **Algae Cylinders**: Used to score endgame points

### 2.2 Core Mechanics

#### 2.2.1 Coral Strength

A critical mechanic is the relative strength between coral types, displayed on the polyp tile scoring track. This determines which coral can consume others.

For example, if red coral has a strength of 4 against blue coral's strength of 2, then red coral can consume blue coral. These strength relationships change during gameplay.

#### 2.2.2 Turn Structure

Each player's turn consists of performing 4 actions (5 actions in a 2-player game) from the following options:

1. **Place a coral tile** from their hand onto the reef board
2. **Grow a coral colony** by adding a matching color larva cube
3. **Adjust coral strength** by placing a polyp tile
4. **Place a shrimp** to protect their coral or prepare for consumption
5. **Consume coral** using a shrimp to capture opponent's coral
6. **Feed their parrotfish** by moving coral tiles into their parrotfish storage

Players can perform the same action multiple times or different actions in any order.

#### 2.2.3 Victory Conditions

The game ends when either:
- The tile supply is exhausted
- All players have fed their parrotfish at least once and one player has no moves available

Final scoring is based on:
- Value of coral tiles in each player's parrotfish
- Multiplied by the number of algae cylinders collected
- Modified by relative coral strengths at game end

### 2.3 Detailed Rules

#### 2.3.1 Coral Growth and Placement

- Coral tiles must be placed adjacent to existing coral or along the reef board edge
- Connected tiles of the same color form a colony
- Colonies must remain connected (important rule to enforce in implementation)
- Larva cubes are placed on corals to grow them

#### 2.3.2 Coral Dominance and Consumption

- A coral colony can only consume another if:
  - It has greater strength (as shown on polyp tiles)
  - The consuming colony has at least as many tiles as the target colony
  - A player's shrimp is adjacent to the target colony
  - The target colony is not protected by an opponent's shrimp

#### 2.3.3 Shrimp Placement and Movement

- Shrimp must be placed adjacent to a coral colony
- Shrimp can be used to:
  - Protect a colony from being consumed
  - Prepare to consume an adjacent opponent's colony

#### 2.3.4 Feeding the Parrotfish

- Players can move coral tiles to their parrotfish storage
- Once a parrotfish has been fed, it cannot be unfed (irreversible action)
- The value of coral in the parrotfish at game end determines the winner

## 3. Technical Implementation

### 3.1 Application Architecture

For a PHP implementation, we recommend:

1. **MVC Architecture**:
   - **Models**: Represent game state, coral colonies, players, etc.
   - **Views**: PHP templates for game board, player actions, scoring
   - **Controllers**: Handle game logic, action validation, turn management

2. **Database Structure**:
   - `games` table: Store game instances and states
   - `players` table: Player information and scores
   - `game_state` table: Current board state, coral positions, strengths
   - `actions` table: Log of player moves for replay/undo functionality

3. **State Management**:
   - Session management for player identification
   - JSON serialization for complex game state storage
   - Transaction-based approach for atomic game state updates

### 3.2 Key Data Structures

```php
// Coral type representation
class CoralType {
    public $color;       // 'red', 'green', 'blue', 'yellow', 'purple'
    public $strengths;   // Associative array of relative strengths vs other colors
}

// Coral tile on the reef board
class CoralTile {
    public $type;        // CoralType reference
    public $position;    // x,y coordinates
    public $larvaCubes;  // Number of larva cubes on this tile
    public $colonyId;    // ID of the colony this tile belongs to
}

// Coral colony (connected group of same-color tiles)
class CoralColony {
    public $id;
    public $type;        // CoralType reference
    public $tiles;       // Array of CoralTile references
    public $isProtected; // Whether a shrimp is protecting this colony
}

// Game board representation
class ReefBoard {
    public $grid;        // 2D array of tile positions
    public $colonies;    // Array of coral colonies
    public $shrimps;     // Array of shrimp positions and owners
}

// Player state
class Player {
    public $id;
    public $name;
    public $color;
    public $hand;        // Array of coral tiles in hand
    public $larvaCubes;  // Counts of available larva cubes by color
    public $parrotfish;  // Array of coral tiles fed to parrotfish
    public $algae;       // Number of algae cylinders collected
    public $shrimps;     // Number of available shrimp tokens
}

// Game state
class GameState {
    public $gameId;
    public $players;     // Array of Player objects
    public $currentTurn; // ID of player whose turn it is
    public $actionsLeft; // Number of actions remaining in current turn
    public $board;       // ReefBoard object
    public $polyps;      // Current state of polyp tiles (relative strengths)
    public $tileSupply;  // Remaining coral tiles in supply
    public $gamePhase;   // Current phase of the game
}
```

### 3.3 Core Functions

```php
// Action validation functions
function canPlaceTile($player, $tile, $position, $gameState) { /* ... */ }
function canGrowCoral($player, $colonyId, $larvaCubeColor, $gameState) { /* ... */ }
function canAdjustStrength($player, $polyp, $gameState) { /* ... */ }
function canPlaceShrimp($player, $position, $gameState) { /* ... */ }
function canConsumeCoral($player, $shrimp, $targetColony, $gameState) { /* ... */ }
function canFeedParrotfish($player, $colony, $gameState) { /* ... */ }

// Action execution functions
function placeTile($player, $tile, $position, $gameState) { /* ... */ }
function growCoral($player, $colonyId, $larvaCubeColor, $gameState) { /* ... */ }
function adjustStrength($player, $polyp, $gameState) { /* ... */ }
function placeShrimp($player, $position, $gameState) { /* ... */ }
function consumeCoral($player, $shrimp, $targetColony, $gameState) { /* ... */ }
function feedParrotfish($player, $colony, $gameState) { /* ... */ }

// Game state management
function initializeGame($players) { /* ... */ }
function startTurn($playerId, $gameState) { /* ... */ }
function endTurn($gameState) { /* ... */ }
function checkGameEnd($gameState) { /* ... */ }
function calculateScores($gameState) { /* ... */ }

// Colony management functions
function identifyColonies($gameState) { /* ... */ }
function checkColonyConnectivity($colonyId, $gameState) { /* ... */ }
function mergeCelonies($colonyId1, $colonyId2, $gameState) { /* ... */ }
```

## 4. Implementation Challenges and Exceptions

### 4.1 Critical Rules to Enforce

1. **Colony Connectivity**: When consuming coral or feeding the parrotfish, ensure all remaining coral colonies remain connected. This requires complex validation:
   ```php
   function wouldBreakColony($colonyId, $tilesToRemove, $gameState) {
       // Remove tiles temporarily
       // Check if remaining tiles in colony are still connected
       // If not connected, return true (would break colony)
   }
   ```

2. **Coral Strength Relationships**: Maintain the cyclic dominance relationships between coral types:
   ```php
   function updateCoralStrengths($polyp, $gameState) {
       // Update the relative strength matrix
       // Ensure each coral has strengths vs all other coral types
   }
   ```

3. **Shrimp Protection**: Enforce rules about shrimp protection and consumption:
   ```php
   function isColonyProtected($colonyId, $gameState) {
       // Check if any shrimp belonging to the colony owner is adjacent
   }
   ```

### 4.2 Edge Cases

1. **Tie-breaking**: Implement detailed tie-breaking rules for end-game scoring
2. **Isolated Tiles**: Handle creation of isolated coral tiles when consuming coral
3. **No Valid Move**: Detect when a player has no valid moves available
4. **Game End Conditions**: Track both end conditions (tile exhaustion or no valid moves)

### 4.3 UI Challenges

1. **Complex Board Visualization**: Representing the 3D nature of coral stacking
2. **Action Selection**: Provide intuitive interface for selecting from multiple actions
3. **Colony Identification**: Visually distinguish different coral colonies
4. **Strength Relationship Display**: Clear visual for current coral strengths

## 5. User Interface Considerations

### 5.1 Game Board Representation

The web interface should display:
- Main reef board with coral tiles and colonies
- Polyp tile track showing current coral strengths
- Player information including:
  - Available resources (larva cubes, coral tiles, shrimp)
  - Parrotfish contents
  - Algae cylinders
  - Actions remaining

### 5.2 Player Interaction

1. **Action Selection**: Clear buttons or menu for selecting actions
2. **Tile Placement**: Click-and-drag or point-and-click interface for placing tiles
3. **Colony Selection**: Highlight valid colonies when an action requires selection
4. **Confirmation Dialogs**: For irreversible actions like feeding the parrotfish
5. **Game State Information**: Display of current strengths, colonies, and options

### 5.3 Accessibility Considerations

1. **Color-blind Friendly**: Use patterns and symbols in addition to colors
2. **Keyboard Navigation**: Allow for navigation without requiring mouse
3. **Undo Functionality**: Where game rules permit
4. **Turn History**: Display log of recent moves

## 6. Conclusion and Recommendations

Implementing Reef Encounter as a web application presents interesting challenges due to its complex tile-placement mechanics and the interconnected nature of coral colonies. The PHP backend should focus on robust game state management and rule enforcement.

### 6.1 Development Approach

1. **Iterative Implementation**:
   - Start with basic board and tile placement functionality
   - Add colony management and growth
   - Implement shrimp placement and coral consumption
   - Add parrotfish feeding and scoring
   - Finally, implement polyp tile management and strength adjustments

2. **Testing Strategy**:
   - Unit tests for individual rule validation
   - Integration tests for turn sequences
   - End-to-end tests for complete game scenarios

3. **Technical Recommendations**:
   - Use PHP 7.4+ for improved type hints and performance
   - Consider a JavaScript frontend (Vue.js or React) for dynamic UI
   - Implement WebSockets for real-time multiplayer functionality
   - Create a robust API for game state management

By focusing on the core mechanics and carefully implementing the connectivity rules, a web-based version of Reef Encounter can successfully capture the strategic depth and unique theme of the physical board game.
