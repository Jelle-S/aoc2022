<?php

$rps = [
  'X' => [
    'beats' => 'C',
    'score' => 1,
    'draws' => 'A',
  ],
  'Y' => [
    'beats' => 'A',
    'score' => 2,
    'draws' => 'B',
  ],
  'Z' => [
    'beats' => 'B',
    'score'=> 3,
    'draws' => 'C',
  ],
];

$handle = fopen('input.txt', 'r');
$score = 0;
while (($line = fgets($handle)) !== false) {
  $plays = explode(' ', $line);
  $yourplay = trim($plays[1]);
  $theirplay = trim($plays[0]);

  $score += $rps[$yourplay]['score'];
  if ($rps[$yourplay]['beats'] === $theirplay) {
    $score += 6;
    continue;
  }
  if ($rps[$yourplay]['draws'] === $theirplay) {
    $score += 3;
    continue;
  }
}

print $score . "\n";
