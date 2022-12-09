<?php


function parse($file) {
  $handle = fopen($file, 'r');

  while (($line = fgets($handle)) !== false && trim($line)) {
    $moves[] = explode(' ', $line);
  }
  return $moves;
}

function solve($moves) {
  // Starting point (0,0) is visited once, as it's the starting point.
  $visitedPoints = ['0,0' => 1];
  $head = ['x' => 0, 'y' => 0];
  $knots = array_fill(0, 9, $head);
  foreach ($moves as $move) {
    for ($i = 0; $i < $move[1]; $i++) {
      $head = makeMove($move[0], $head);
      $previousKnot = $head;
      foreach ($knots as $knotNum => $knot) {
        $knots[$knotNum] = adjustTail($knot, $previousKnot);
        $previousKnot = $knots[$knotNum];
      }
      $tail = end($knots);
      $visitedPoints[$tail['x'] . ',' . $tail['y']] = 1;
    }
  }

  return count($visitedPoints);
}

function makeMove($direction, $head) {
  $velocity = in_array($direction, ['U', 'L']) ? -1 : 1;
  $coordinate = in_array($direction, ['U', 'D']) ? 'y' : 'x';

  $head[$coordinate] += $velocity;

  return $head;
}

function adjustTail($tail, $head) {
  $x = $head['x'] - $tail['x'];
  $y = $head['y'] - $tail['y'];

  switch(true) {
    // Not is same row _or_ column, move diagonally.
    case abs($x) + abs($y) > 2:
      $tail['x'] += $x > 0 ? 1 : -1;
      $tail['y'] += $y > 0 ? 1 : -1;
      break;
    case abs($x) > 1:
      $tail['x'] += $x > 0 ? 1 : -1;
      break;
    case (abs($y) > 1):
      $tail['y'] += $y > 0 ? 1 : -1;
      break;
  }

  return $tail;
}

print solve(parse('input.txt')) . PHP_EOL;
