<?php
$items = array_map('chr', array_merge(range(97,122), range(65, 90)));
$handle = fopen('input.txt', 'r');
$priority = 0;
$group = [];
while (($line = fgets($handle)) !== false) {
  $line = trim($line);
  $group[] = array_unique(str_split($line));
  if (count($group) === 3) {
    $badge = array_intersect(...$group);
    $priority += array_search(reset($badge), $items) +1 ;
    $group = [];
  }
}

print $priority . "\n";
