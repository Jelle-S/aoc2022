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

function solve($pairs, $minBoundary, $maxBoundary) {
  $pairs = array_map('addDistanceToPair', $pairs);
  $mergedIntersections = [];
  for ($i = $minBoundary; $i <= $maxBoundary; $i++) {
    $intersections = [];
    foreach ($pairs as $pair) {
      $intersection = calculateIntersectionXCoordinates($pair, $i);
      if ($intersection && $reducedIntersection = reduceIntersection($intersection, $minBoundary, $maxBoundary)) {
        $intersections[] = $reducedIntersection;
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

    if (count($mergedIntersections) > 1) {
      break;
    }

    $intersectionWidth = array_reduce(
      $mergedIntersections,
      function($carry, $item) { return $carry + abs($item[0] - $item[1]) + 1; },
      0
    );
    if ($intersectionWidth !== ($maxBoundary - $minBoundary + 1)) {
      break;
    }
  }

  $y = $i;
  $x = array_values(array_filter(
    range($minBoundary, $maxBoundary),
    function ($value) use ($mergedIntersections) {
      foreach ($mergedIntersections as $intersection) {
        if (isOverLapping($intersection, [$value, $value])) {
          return false;
        }
      }
      return true;
    }
  ))[0];
  return ($x * 4000000) + $y;
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

function reduceIntersection($intersection, $minBoundary, $maxBoundary) {
  $intersection[0] = max($intersection[0], $minBoundary);
  $intersection[1] = min($intersection[1], $maxBoundary);

  return $intersection[0] <= $intersection[1] ? $intersection : [];
}

print solve(parse('input.txt'), 0, 4000000) . PHP_EOL;
//print solve(parse('sample.txt'), 0, 20) . PHP_EOL;
