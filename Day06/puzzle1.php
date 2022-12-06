<?php
$buffer = str_split(file_get_contents('input.txt'));
$marker_length = 4;
$marker = [];
$index = 0;
while(count($buffer)) {
  $index++;
  $marker[] = array_shift($buffer);
  if (count($marker) > $marker_length) {
    array_shift($marker);
  }
  if (count($marker) === $marker_length && count(array_unique($marker)) === $marker_length) {
    break;
  }
}

echo $index;
