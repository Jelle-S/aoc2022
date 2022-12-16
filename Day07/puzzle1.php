<?php

include_once './TreeNode.php';
include_once './Parser.php';

$parser = new Parser();
$parser->parse('input.txt');

$size = 0;
foreach ($parser->findDirectoriesWithMaxSize(100000) as $directory) {
  $size += $directory->getSize();
}

echo $size . PHP_EOL;