<?php

include_once './TreeNode.php';
include_once './Parser.php';

$parser = new Parser();
$parser->parse('input.txt');
$totalSpace = 70000000;
$usedSpace = $parser->getRoot()->getSize();
$freeSpace = $totalSpace - $usedSpace;
$requiredSpace = 30000000;
$spaceToFree = $requiredSpace - $freeSpace;

echo $parser->getDirectoryClosestToMinSize($spaceToFree)->getSize() . PHP_EOL;
