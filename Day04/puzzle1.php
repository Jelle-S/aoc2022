<?php

function hasFullOverlap(...$ranges) {
  usort($ranges, function ($rangeA, $rangeB) {
    $cmp = $rangeA[0] - $rangeB[0];

    return $cmp === 0 ? $rangeB[1] - $rangeA[1] : $cmp;
  });

  return $ranges[0][0] <= $ranges[1][0] && $ranges[0][1] >= $ranges[1][1];

}

$handle = fopen('input.txt', 'r');
$overlaps = 0;
while (($line = fgets($handle)) !== false) {
  $line = trim($line);
  $matches = [];
  preg_match_all('/^(\d+)-(\d+),(\d+)-(\d+)$/', $line, $matches);
  $ranges = [
    [intval($matches[1][0]), intval($matches[2][0])],
    [intval($matches[3][0]), intval($matches[4][0])],
  ];
  if (hasFullOverlap(...$ranges)) {
    $overlaps++;
  }
}

print $overlaps . "\n";
