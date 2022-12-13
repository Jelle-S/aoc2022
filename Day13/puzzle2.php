<?php


function parse($file) {
  $packetStrings = array_filter(explode("\n", file_get_contents($file) . "\n[[2]]\n[[6]]"));
  return array_combine(
    $packetStrings,
    array_map(
      'json_decode',
      $packetStrings
    )
  );
}

function solve($packets) {
  uasort($packets, 'comparePackets');
  $indexes = array_keys(array_intersect(array_keys($packets), ['[[2]]', '[[6]]']));
  // Add one per match, since pairs start at index 1 (fix off by one).
  return ($indexes[0] + 1) * ($indexes[1] + 1) ;
}

function isOrderedCorrectly($pairs) {
  return comparePackets($pairs[0], $pairs[1]) <= 0;
}

function comparePackets($leftPacket, $rightPacket) {
  // Both ints.
  if (is_numeric($leftPacket) && is_numeric($rightPacket)) {
    return $leftPacket - $rightPacket;
  }

  // One int, one array.
  if (is_numeric($leftPacket) || is_numeric($rightPacket)) {
    return comparePackets((array) $leftPacket, (array) $rightPacket);
  }

  // Both arrays.
  $key = -1;
  foreach ($rightPacket as $key => $packet) {
    // Left runs out first.
    if (!isset($leftPacket[$key])) {
      return -1;
    }
    $result = comparePackets($leftPacket[$key], $rightPacket[$key]);
    if ($result !== 0) {
      return $result;
    }
  }

  // Did right run out first?
  return isset($leftPacket[$key+1]) ? 1 : 0;
}

print solve(parse('input.txt')) . PHP_EOL;
