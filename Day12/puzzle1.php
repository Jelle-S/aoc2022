<?php

include_once 'Dijkstra.php';

function parse($file) {
  $handle = fopen($file, 'r');
  $grid = [];
  $x = $y = 0;
  while (($line = fgets($handle)) !== false && trim($line)) {
    foreach (str_split(trim($line)) as $x => $gridItem) {
      if ($gridItem === 'S') {
        $start = ['x' => $x, 'y' => $y, 'data' => 'a'];
        $gridItem = 'a';
      }
      if ($gridItem === 'E') {
        $destination = ['x' => $x, 'y' => $y, 'data' => 'z'];
        $gridItem = 'z';
      }
      $grid[$y][$x] = $gridItem;
    }
    $y++;
  }
  return [$start, $destination, $grid];
}

function solve($start, $destination, $grid) {
  $dijkstra = new Dijkstra($grid, function ($item, $neighbour) {
    return ord($neighbour['data']) - ord($item['data']) > 1 ? false : 1;
  });
  $path = $dijkstra->calculate($start, $destination);

  return $path['distance'];
}

print solve(...parse('input.txt')) . PHP_EOL;
