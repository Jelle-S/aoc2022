<?php

include_once __DIR__ . DIRECTORY_SEPARATOR . 'Monkey.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . 'MonkeyInTheMiddle.php';

function parse($file) {
  return explode("\n\n", file_get_contents($file));
}

function solve($monkeyDescriptions) {
  $monkeys = [];
  foreach ($monkeyDescriptions as $monkeyDescription) {
    $monkeys[] = Monkey::fromDescription($monkeyDescription);
  }
  $game = new MonkeyInTheMiddle($monkeys);
  $game->play(20);
  $sortedMonkeys = $game->getMonkeysByActivity();
  return $sortedMonkeys[0]->getNumberOfInspectedItems() * $sortedMonkeys[1]->getNumberOfInspectedItems();
}

print solve(parse('input.txt')) . PHP_EOL;
