<?php
$words = ['RUST', 'ZIG', 'CPLUSPLUS', 'PHP', 'ASTROPHYSICS', 'QUANTUM'];

// Function to determine which hangman image to display based on wrong guesses
function getHangmanImage($wrongGuesses)
{
    return "H" . $wrongGuesses . ".GIF";
}

// Initialize variables from form submission
$guess = isset($_POST['txtGuess']) ? strtoupper(trim($_POST['txtGuess'])) : '';
$guessedLetters = isset($_POST['hidGuessed']) ? $_POST['hidGuessed'] : '';
$rightGuesses = isset($_POST['hidRite']) ? (int)$_POST['hidRite'] : 0;
$wrongGuesses = isset($_POST['hidRong']) ? (int)$_POST['hidRong'] : 0;

// This is where I made the variable to select a random word from words
$seekritWord = isset($_POST['hidWord']) ? $_POST['hidWord'] : $words[array_rand($words)];
$gameOver = ($wrongGuesses >= 9);
$message = "";

// Checking that there's a guess and the game is still going
if (!empty($guess) && !$gameOver) {
    // ctype_alpha() is checking to make sure the string is a character type and we're also checking strlen to make sure the user only inputs 1 char
    if (strlen($guess) == 1 && ctype_alpha($guess)) {
        // Using the === operator we talked about in class to compare guessedLetters with guess to make sure they're the same type
        if (strpos($guessedLetters, $guess) === false) {
            // This is the string concatination operator that I found I can use to add the user's guess to the guessedLetters variable
            $guessedLetters .= $guess;

            // This is Evil Hangman, so every guess is a wrong guess :)
            $wrongGuesses++;

            // Check if game over after this guess
            if ($wrongGuesses >= 9) {
                $gameOver = true;
            }
        } else {
            $message = "You already guessed that letter!";
        }
    } else {
        $message = "Please enter a single letter.";
    }
}

// Get the current image
$currentImage = getHangmanImage($wrongGuesses);


$wordLength = strlen($seekritWord);
$displayWord = "";

for ($i = 0; $i < $wordLength; $i++) {
    $displayWord .= "_ ";
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Evil Hangman</title>
</head>

<body>
  <h1>Evil Hangman</h1><br />
  <!-- I used drop-ins for the variables like in the instrucitons, but I also figured
    out that I can drop php functions with drop-in tags as well. I think this setup is pretty cool -->
  <?php if ($gameOver): ?>
    <p>Game Over! You lost!</p>
    <p>The word was: <?= $seekritWord ?> </p>
    <img src='<?= $currentImage ?>' border=1><br /><br>
    <a href="hman0.htm">Play Again</a>
  <?php else: ?>
    <p>Guess a letter, and click 'Guess!'</p>

    <?php if (!empty($message)): ?>
      <p style="color: red;"><?= $message ?></p>
    <?php endif; ?>

    <p>Word: <?= $displayWord ?></p>

    <p>Guessed letters: <?= chunk_split($guessedLetters, 1, " ") ?></p>

    <form action="hman0.php" method="POST" name="form1">
      <input type='text' name='txtGuess' value='' maxlength="1" />
      <input type='hidden' name='hidRite' value='<?= $rightGuesses ?>' />
      <input type='hidden' name='hidRong' value='<?= $wrongGuesses ?>' />
      <input type='hidden' name='hidGuessed' value='<?= $guessedLetters ?>' />
      <input type='hidden' name='hidWord' value='<?= $seekritWord ?>' />
      <input type='submit' value='Guess!' name='btnGuess' />
    </form><br>

    <img src='<?= $currentImage ?>' border=1><br /><br>
  <?php endif; ?>
</body>

</html>
