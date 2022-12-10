<?php


function parse($file) {
  $handle = fopen($file, 'r');
  $buffer = [];
  while (($line = fgets($handle)) !== false && trim($line)) {
    foreach (explode(' ', $line) as $cycle) {
      $buffer[] = trim($cycle);
    }
  }
  return $buffer;
}

function solve($buffer) {
  $relevantCycles = [20, 60, 100, 140, 180, 220];
  $signalStrengths = [];
  $strength = 0;
  array_unshift($buffer, 1);
  foreach ($buffer as $cycle => $adjustment) {
    if (in_array($cycle, $relevantCycles)) {
      $signalStrengths[] = $cycle * $strength;
    }
    if (is_numeric($adjustment)) {
      $strength += $adjustment;
    }
  }

  return array_sum($signalStrengths);
}

print solve(parse('input.txt')) . PHP_EOL;
