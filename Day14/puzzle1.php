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

  fillGrid(['x' => $minX, 'y' => $minY], ['x' => $maxX, 'y' => $maxY], $grid);
  return [$grid, ['x' => 500, 'y' => 0]];
}

function solve($grid, $startPoint) {
  $sandGrainPosition = false;
  $sandGrains = 0;
  do {
    if ($sandGrainPosition) {
      $grid[$sandGrainPosition['y']][$sandGrainPosition['x']] = SAND_CHAR;
      $sandGrains++;
    }
    $dijkstra = new Dijkstra($grid, Closure::fromCallable('distanceCalculator'));
    $dijkstra->setNeighbourCoordinatesCalculator(function ($item) use ($grid) {
      // Only one neighbour matters really.
      foreach ([0, -1, 1] as $x) {
        // Best match is out of bounds, sand grain drops into the void.
        if (!isset($grid[$item['y'] + 1][$item['x'] + $x])) {
          return [];
        }
        $neighbour = $grid[$item['y'] + 1][$item['x'] + $x];
        // Best match is blocked, continue to the next one.
        if ($neighbour === ROCK_CHAR || $neighbour === SAND_CHAR) {
          continue;
        }

        return [['x' => $item['x'] + $x, 'y' => $item['y'] + 1]];
      }
    });
    $sandGrainPosition = $dijkstra->calculate($startPoint, function ($item) use ($grid) { return hasReachedRestingPoint($item, $grid); });
  } while ($sandGrainPosition);
  //printGrid($grid);
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
  }
}

function fillGrid($topLeft, $bottomRight, &$grid, $char = AIR_CHAR) {
  for($y = $topLeft['y']; $y <= $bottomRight['y']; $y++) {
    for ($x = $topLeft['x']; $x <= $bottomRight['x']; $x++) {
      if (!isset($grid[$y][$x])) {
        $grid[$y][$x] = $char;
      }
    }
    ksort($grid[$y]);
  }
  ksort($grid);
}

function distanceCalculator($item, $neighbour) {
  $distances = [
    -1 => 2,
    0 => 0,
    1 => 1,
  ];

  return $distances[$item['x'] - $neighbour['x']];
}

function neighbourCalculator($item) {
  foreach ([-1, 0, 1] as $x) {
    yield ['x' => $item['x'] + $x, 'y' => $item['y'] + 1];
  }
}

function hasReachedRestingPoint($sandGrainPosition, $grid) {
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

print solve(...parse('input.txt')) . PHP_EOL;
