<?php

$english = [
    1 => 'one',
    2 => 'two',
    3 => 'three',
    4 => 'four',
    5 => 'five',
    6 => 'six'
  ];

// $roll = 0;

// while ($roll !== 6) {
//     $roll = rand(1, 6);
//     echo '<p>You rolled a ' . $roll . '</p>';

//     if ($roll === 6) {
//         echo '<p>You win!<p>';
//     } else {
//         echo '<p>Sorry, you didn\'t win, better luck next time!</p>';
//     }
// }

do {
    $roll = rand(1, 6);
    echo '<p>You rolled a ' . $english[$roll] . '</p>';

    if ($roll === 6) {
        echo '<p>You win!<p>';
    } else {
        echo '<p>Sorry, you didn\'t win, better luck next time!</p>';
    }
}
while ($roll !== 6);