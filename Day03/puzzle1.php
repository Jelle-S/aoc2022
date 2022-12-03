<?php
$items = array_map('chr', array_merge(range(97,122), range(65, 90)));
$handle = fopen('input.txt', 'r');
$priority = 0;
while (($line = fgets($handle)) !== false) {
  $line = trim($line);
  $compartments = [
    str_split(substr($line, 0, strlen($line)/2)),
    str_split(substr($line, strlen($line)/2))
  ];
  $matched_items = array_intersect($compartments[0], $compartments[1]);
  $priority += array_search(reset($matched_items), $items) + 1;
}

print $priority . "\n";
