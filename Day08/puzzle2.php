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
  $max = 0;
  foreach ($grid as $y => $row) {
    foreach ($row as $x => $height) {
      $max = max(getScenicScore(['x' => $x, 'y' => $y, 'height' => $height], $grid), $max);
    }
  }
  return $max;
}

function getScenicScore($tree, $grid) {
  return getScenicScoreColumn($tree, $grid) * getScenicScoreRow($tree, $grid);
}

function getScenicScoreRow($tree, $grid) {
  $scenicScoreLeft = $scenicScoreRight = 0;
  $row = forrestRow($tree['y'], $grid);

  $treesToTheLeft = array_slice($row, 0, $tree['x']);
  while(($treeHeight = array_pop($treesToTheLeft)) !== null) {
    $scenicScoreLeft ++;
    if ($treeHeight >= $tree['height']) {
      break;
    }
  }

  $treesToTheRight = array_slice($row, $tree['x'] + 1);
  while(($treeHeight = array_shift($treesToTheRight)) !== null) {
    $scenicScoreRight ++;
    if ($treeHeight >= $tree['height']) {
      break;
    }
  }

  return $scenicScoreLeft * $scenicScoreRight;
}

function getScenicScoreColumn($tree, $grid) {
  $scenicScoreAbove = $scenicScoreBelow = 0;
  $column = forrestColumn($tree['x'], $grid);

  $treesAbove = array_slice($column, 0, $tree['y']);
  while(($treeHeight = array_pop($treesAbove)) !== null) {
    $scenicScoreAbove ++;
    if ($treeHeight >= $tree['height']) {
      break;
    }
  }

  $treesBelow = array_slice($column, $tree['y'] + 1);
  while(($treeHeight = array_shift($treesBelow)) !== null) {
    $scenicScoreBelow ++;
    if ($treeHeight >= $tree['height']) {
      break;
    }
  }

  return $scenicScoreAbove * $scenicScoreBelow;
}

function forrestColumn($column, $grid) {
  return array_map(function ($row) use ($column) {
    return $row[$column];
  }, $grid);
}

function forrestRow($row, $grid) {
  return $grid[$row];
}

print solve(parse('input.txt')) . PHP_EOL;
