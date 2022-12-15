<?php

// See context.md

function parse($file) {
  // Sensor beacon pairs.
  $pairs = [];
  $regex = '/Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)/';

  $handle = fopen($file, 'r');
  while (($line = fgets($handle)) !== false && trim($line)) {
    $matches = [];
    preg_match($regex, $line, $matches);
    unset($matches[0]);
    $pairs[] = array_combine(['xs', 'ys', 'xb', 'yb'], array_map('intval', array_values($matches)));
  }

  return $pairs;
}

function solve($pairs, $intersectionRow) {
  $pairs = array_map('addDistanceToPair', $pairs);
  $intersections = [];
  $beaconsInRow = [];
  foreach ($pairs as $pair) {
    if ($pair['yb'] === $intersectionRow) {
      $beaconsInRow[] = $pair['xb'];
    }
    $intersection = calculateIntersectionXCoordinates($pair, $intersectionRow);
    if ($intersection) {
      $intersections[] = $intersection;
    }
  }

  // Sort the intersections from smallest min x to largest min x, so we can
  // merge overlapping or bordering intersections.
  usort($intersections, function($intersectionA, $intersectionB) { return $intersectionA[0] - $intersectionB[0]; });

  // Merge overlapping/bordering intersections.
  $mergedIntersections = [];
  foreach ($intersections as $intersection) {
    $mergedIntersections = mergeOrAddNew($mergedIntersections, $intersection);
  }

  $amountOfBeaconsInIntersections = array_reduce(
    $mergedIntersections,
    function($carry, $item) use ($beaconsInRow) {
      foreach ($beaconsInRow as $beaconX) {
        if (isOverLapping($item, [$beaconX, $beaconX])) {
          return $carry + 1;
        }
      }

      return $carry;
    },
    0
  );

  return array_reduce(
    $mergedIntersections,
    function($carry, $item) { return $carry + abs($item[0] - $item[1]) + 1; },
    0
  ) - $amountOfBeaconsInIntersections;
}

function addDistanceToPair($pair) {
  $pair['distance'] = abs($pair['xs'] - $pair['xb']) + abs($pair['ys'] - $pair['yb']);

  return $pair;
}

function mergeOrAddNew($intersections, $intersection) {
  foreach ($intersections as $key => $existing) {
    // One of the coordinates is part of an existing intersection? Merge them:
    if (isOverlappingOrBordering($existing, $intersection)) {
      $merged = array_merge($existing, $intersection);
      $intersections[$key] = [min($merged), max($merged)];
      return $intersections;
    }
  }
  $intersections[] = $intersection;
  return $intersections;
}

function isOverlappingOrBordering($intersectionA, $intersectionB) {
  // Overlapping.
  return isOverLapping($intersectionA, $intersectionB)
    // Bordering.
    || isBordering($intersectionA, $intersectionB);
}

function isOverLapping($intersectionA, $intersectionB) {
  return ($intersectionA[1] >= $intersectionB[0] && $intersectionA[0] <= $intersectionB[1]);
}

function isBordering($intersectionA, $intersectionB) {
  return ($intersectionB[1] + 1 === $intersectionA[0] || $intersectionA[1] + 1 === $intersectionB[0]);
}

function calculateIntersectionXCoordinates($pair, $yr) {
  // First x coordinate:
  // xr = -| xs - xb | - | ys - yb | + | ys - yr | + xs
  $xr1 = -abs($pair['xs'] - $pair['xb']) - abs($pair['ys'] - $pair['yb']) + abs($pair['ys'] - $yr) + $pair['xs'];

  // Does the distance match? If not: no intersection!
  $distance = abs($xr1 - $pair['xs']) + abs($yr - $pair['ys']);
  if ($distance !== $pair['distance']) {
    return [];
  }

  // Second x coordinate:
  // xr = | xs - xb | + | ys - yb | - | ys - yr | + xs
  $xr2 = abs($pair['xs'] - $pair['xb']) + abs($pair['ys'] - $pair['yb']) - abs($pair['ys'] - $yr) + $pair['xs'];

  $coordinates = [$xr1, $xr2];
  sort($coordinates);
  return $coordinates;

}

print solve(parse('input.txt'), 2000000) . PHP_EOL;
//print solve(parse('sample.txt'), 10) . PHP_EOL;
