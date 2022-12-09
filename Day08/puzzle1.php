<?php


function parse($file) {
  $handle = fopen($file, 'r');
  $grid = [];
  while (($line = fgets($handle)) !== false) {
    $grid[] = array_map('intval', str_split(trim($line)));
  }
  return $grid;
}

function solve($grid) {
  $visibleForrest = [];
  foreach ($grid as $y => $row) {
    foreach ($row as $x => $height) {
      if (isVisible(['x' => $x, 'y' => $y, 'height' => $height], $grid)) {
        $visibleForrest[$y][$x] = $height;
      }
    }
  }
  //printForrest($visibleForrest);
  return numberOfTreesInForrest($visibleForrest);
}

function isVisible($tree, $grid) {
  $column = forrestColumn($tree['x'], $grid);
  $row = forrestRow($tree['y'], $grid);

  return isVisibleInRow($tree, $row) || isVisibleInColumn($tree, $column);

}

function isVisibleInRow($tree, $row) {
  $treesToTheLeft = array_slice($row, 0, $tree['x']);
  if (!$treesToTheLeft || max($treesToTheLeft) < $tree['height']) {
    return true;
  }
  $treesToTheRight = array_slice($row, $tree['x'] + 1);
  if (!$treesToTheRight || max($treesToTheRight) < $tree['height']) {
    return true;
  }

  return false;
}


function isVisibleInColumn($tree, $column) {
  $treesAbove = array_slice($column, 0, $tree['y']);
  if (!$treesAbove || max($treesAbove) < $tree['height']) {
    return true;
  }
  $treesBelow = array_slice($column, $tree['y'] + 1);
  if (!$treesBelow || max($treesBelow) < $tree['height']) {
    return true;
  }

  return false;
}

function printForrest($forrest) {
  for ($i = 0; $i < count($forrest); $i++) {
    for ($j = 0; $j < count($forrest[0]); $j++) {
      print (isset($forrest[$i]) ? ($forrest[$i][$j] ?? '.') : '.');
    }
    print PHP_EOL;
  }
  print PHP_EOL;
}

function forrestColumn($column, $grid) {
  return array_map(function ($row) use ($column) {
    return $row[$column];
  }, $grid);
}

function forrestRow($row, $grid) {
  return $grid[$row];
}

function numberOfTreesInForrest($visibleForrest) {
  return array_reduce($visibleForrest, function($carry, $item) {
    return $carry + count($item);
  }, 0);
}

print solve(parse('input.txt')) . PHP_EOL;