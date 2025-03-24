<!Doctype html>
<html>

<head>
    <title>Lab 1c</title>
</head>

<body>
    <h2>Automated 2D Array Example With 20 Columns</h2>
    <?php
    // Fixed example with 20 columns
    $fixed_colno = 20;
    $ordletstart = ord('A');
    $ordletend = ord('K');

    $aryc = array(1 => '');
    $ary = array(65 => $aryc);

    echo '<pre>';
    for ($r = $ordletstart; $r <= $ordletend; $r++) {
        for ($c = 1; $c <= $fixed_colno; $c++) {
            $ary[$r][$c] = chr($r) . '-' . $c;
        }
    }

    // This is where i start looping through everything to build the correct table
    for ($r = $ordletstart - 1; $r <= $ordletend + 1; $r++) {
        // Loop through columns (including header/footer column)
        for ($c = 0; $c <= $fixed_colno + 1; $c++) {
            if ($r == $ordletstart - 1 || $r == $ordletend + 1) {
                // Header/footer row
                if ($c == 0 || $c == $fixed_colno + 1) {
                    echo '      ';  // Corner space
                } else {
                    echo str_pad($c, 6, ' ', STR_PAD_BOTH);  // Column number
                }
            } else if ($c == 0 || $c == $fixed_colno + 1) {
                // Header/footer column
                if ($c == 0) {
                    echo '  ' . chr($r) . '   ';  // This is where I figured out how to space the row headers correctly
                } else {
                    echo '   ' . chr($r) . '  ';  // This is for the right side
                }
            } else {
                // I'm tinkering with spacing here to get things to look better
                // But this is whe cells get built
                echo str_pad($ary[$r][$c], 6, ' ', STR_PAD_BOTH);
            }
        }
        echo "\n";  // Made a new line after each row
    }
    echo '</pre>';
    ?>

    <h3>Create Your Own Custom Array</h3>
    <form method="post">
        Enter number of columns (2-20):
        <!-- This took me a bit to understand and do, but I like how it works.
        It starts by checking if colno exists in POST data. If yes, use that value; if not, default to 20 -->
        <input type="number" name="colno" min="2" max="20" value="<?php echo isset($_POST['colno']) ? $_POST['colno'] : 0; ?>">
        <input type="submit" value="Submit">
    </form>

    <?php
    if (isset($_POST['colno'])) {
        $colno = $_POST['colno'];
        echo '<h4>Your Custom Array with ' . $colno . ' Columns</h4>';

        // Reset array for custom display
        $aryc = array(1 => '');
        $ary = array(65 => $aryc);

        echo '<pre>';
        for ($r = $ordletstart; $r <= $ordletend; $r++) {
            for ($c = 1; $c <= $colno; $c++) {
                $ary[$r][$c] = chr($r) . '-' . $c;
            }
        }

        // This is where i start looping through everything to build the correct table
        for ($r = $ordletstart - 1; $r <= $ordletend + 1; $r++) {
            // Loop through columns (including header/footer column)
            for ($c = 0; $c <= $colno + 1; $c++) {
                if ($r == $ordletstart - 1 || $r == $ordletend + 1) {
                    // Header/footer row
                    if ($c == 0 || $c == $colno + 1) {
                        echo '      ';  // Corner space
                    } else {
                        echo str_pad($c, 6, ' ', STR_PAD_BOTH);  // Column number
                    }
                } else if ($c == 0 || $c == $colno + 1) {
                    // Header/footer column
                    if ($c == 0) {
                        echo '  ' . chr($r) . '   ';  // This is where I figured out how to space the row headers correctly
                    } else {
                        echo '   ' . chr($r) . '  ';  // This is for the right side
                    }
                } else {
                    // I'm tinkering with spacing here to get things to look better
                    // But this is whe cells get built
                    echo str_pad($ary[$r][$c], 6, ' ', STR_PAD_BOTH);
                }
            }
            echo "\n";  // Made a new line after each row
        }
        echo '</pre>';

        echo '<br><br><br>';
        echo 'The number of elements in $ary: ' . count($ary) . "\n<br>";
        echo 'The rec-total of elements: ', count($ary, COUNT_RECURSIVE), "\n<br>";
    }
    ?>
</body>

</html>
