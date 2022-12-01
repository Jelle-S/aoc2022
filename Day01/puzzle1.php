<?php

$input = file_get_contents('input.txt');

$elves = array_map(function ($elf_items) {
  return array_sum(explode("\n", $elf_items));
}, explode("\n\n", $input));

print max($elves);
