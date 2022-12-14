<?php

// Reusability, yay!
include_once '../Day12/Dijkstra.php';

const ROCK_CHAR = '#';
const AIR_CHAR = '.';
const SAND_CHAR = 'o';

function parse($file) {
  $grid = [[]];
  $handle = fopen($file, 'r');
  $minX = $maxX = 500;
  $minY = $maxY = 0;
  while (($line = fgets($handle)) !== false && trim($line)) {
    $prevCoords = null;
    foreach (explode(' -> ', $line) as $coords) {
      $currentCoords = array_combine(['x', 'y'], array_map('intval', explode(',', $coords)));
      $minX = min($minX, $currentCoords['x']);
      $minY = min($minY, $currentCoords['y']);
      $maxX = max($maxX, $currentCoords['x']);
      $maxY = max($maxY, $currentCoords['y']);
      if ($prevCoords) {
        drawLine($prevCoords, $currentCoords, $grid);
      }
      $prevCoords = $currentCoords;
    }
  }
  // For the bottom line.
  $maxY += 2;
  // Ifinitely wide ground, so add at least one air column on each side. We'll
  // expand as needed.
  $minX--;
  $maxX++;
  drawLine(['x' => $minX, 'y' => $maxY], ['x' => $maxX, 'y' => $maxY], $grid);
  return [$grid, ['x' => 500, 'y' => 0]];
}

function solve($grid, $startPoint) {
  // Wanting to be fancy in part one has come to bite me in the behind.
  $sandGrainPosition = $startPoint;
  $sandGrains = 0;
  while (!hasReachedRestingPoint($sandGrainPosition, $grid)) {
    $y = $sandGrainPosition['y'] + 1;
    foreach ([0, -1, 1] as $dx) {
      $x = $sandGrainPosition['x'] + $dx;
      if (!isset($grid[$y][$x])) {
        expandGrid($x, $grid);
      }
      if (!isset($grid[$y][$x]) || $grid[$y][$x] === AIR_CHAR) {
        $sandGrainPosition = ['x' => $x, 'y' => $y];
        if (hasReachedRestingPoint($sandGrainPosition, $grid)) {
          $grid[$y][$x] = SAND_CHAR;
          $sandGrainPosition = $startPoint;
          $sandGrains++;
        }
        break;
      }
    }
  }
  // Add the last sandgrain. It'll block the flow.
  $grid[$startPoint['y']][$startPoint['x']] = SAND_CHAR;
  $sandGrains++;
  return $sandGrains;
}

function drawLine($from, $to, &$grid, $char = ROCK_CHAR) {
  // Assume lines are always perfectly horizontal or vertical.
  $variableCoord = $from['x'] === $to['x'] ? 'y' : 'x';
  $staticCoord = $variableCoord === 'x' ? 'y' : 'x';
  foreach (range($from[$variableCoord], $to[$variableCoord]) as $coord) {
    ${$variableCoord} = $coord;
    ${$staticCoord} = $from[$staticCoord];
    $grid[$y][$x] = $char;
    ksort($grid[$y]);
  }
  ksort($grid);
}

function hasReachedRestingPoint($sandGrainPosition, $grid) {
  // Sandgrain is at the bottom.
  if ($sandGrainPosition['y'] + 1 === max(array_keys($grid))) {
    return true;
  }
  foreach ([-1, 0, 1] as $dx) {
    $x = $sandGrainPosition['x'] + $dx;
    $y = $sandGrainPosition['y'] + 1;
    if (!isset($grid[$y][$x]) || $grid[$y][$x] === AIR_CHAR) {
      return false;
    }
  }

  return true;
}

function printGrid($grid) {
  print implode("\n", array_map(function ($val) { return implode('', $val); }, $grid)) . PHP_EOL;
}

function expandGrid($x, &$grid) {
  // Expand sideways.
  $bottom = max(array_keys($grid));
  $grid[$bottom][$x] = ROCK_CHAR;
}

print solve(...parse('input.txt')) . PHP_EOL;
