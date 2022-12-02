<?php

$rps = [
  // Rock.
  'A' => [
    // Lose: You play scissors -> 0 + 3
    'X' => 3,
    // Draw: You play rock -> 3 + 1
    'Y' => 4,
    // Win: You play paper -> 6 + 2
    'Z' => 8,
  ],
  // Paper
  'B' => [
    // Lose: You play rock -> 0 + 1
    'X' => 1,
    // Draw: You play paper -> 3 + 2
    'Y' => 5,
    // Win: You play scissors -> 6 + 3
    'Z' => 9,
  ],
  // Scissors
  'C' => [
    // Lose: You play paper -> 0 + 2
    'X' => 2,
    // Draw: You play scissors -> 3 + 3
    'Y' => 6,
    // Win: You play rock -> 6 + 1
    'Z' => 7,
  ],
];

$handle = fopen('input.txt', 'r');
$score = 0;
while (($line = fgets($handle)) !== false) {
  $plays = explode(' ', $line);
  $outcome = trim($plays[1]);
  $theirplay = trim($plays[0]);

  $score += $rps[$theirplay][$outcome];
}

print $score . "\n";
