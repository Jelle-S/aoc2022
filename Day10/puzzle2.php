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
  // Using ' ' and not '.' because my old eyes can't read the letters otherwise.
  $screen = array_fill(0, 6, array_fill(0, 40, ' '));
  $row = 0;
  $relevantCycles = [40, 80, 120, 160, 200, 240];
  $signalStrengths = [];
  $spritePosition = $drawingPosition = 0;
  array_unshift($buffer, 1);
  foreach ($buffer as $cycle => $adjustment) {
    if (in_array($cycle, $relevantCycles)) {
      $row++;
    }

    if (is_numeric($adjustment)) {
      $spritePosition += $adjustment;
    }
    $drawingPosition = ($cycle) % 40;
    if (in_array($drawingPosition, range($spritePosition - 1, $spritePosition + 1))) {
      $screen[$row][$drawingPosition] = '#';
    }
  }

  return $screen;
}

function print_screen($screen) {
  print implode(
    PHP_EOL,
    array_map(
      function ($row) {
        return implode('', $row);
      },
      $screen
    )
  ) . PHP_EOL;
}

print_screen(solve(parse('input.txt'))) . PHP_EOL;
