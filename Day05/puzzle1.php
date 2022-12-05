<?php

/**
 * @return \SplStack[]
 */
function parseStacks(array $stackLines, $stackAmount) {
  $stacks = [];
  for ($i = 0; $i < $stackAmount; $i++) {
    $stack = new SplStack();
    $stacks[] = $stack;
  }
  // SplStack is LIFO, but we're reading top to bottom, so reverse the lines.
  foreach (array_reverse($stackLines) as $stackLine) {
    $crates = array_map('trim', str_split($stackLine, 4));
    foreach ($crates as $stackNumber => $crate) {
      if ($crate) {
        $stacks[$stackNumber]->push($crate);
      }
    }
  }

  return $stacks;
}

function parseMove($line) {
  $matches = [];
  preg_match('/move\s+(\d+)\s+from\s+(\d+)\s+to\s+(\d+)/', $line, $matches);
  array_shift($matches);
  return $matches;
}

$handle = fopen('input.txt', 'r');
$stackLines = [];
$stackAmount = 0;
while (($line = fgets($handle)) !== false) {
  if (!trim($line)) {
    // Parsed all stacks, last line is stack numbers.
    $stackAmount = preg_match_all('/\s\d+/', array_pop($stackLines));
    break;
  }
  $stackLines[] = $line;
}

$stacks = parseStacks($stackLines, $stackAmount);

while (($line = fgets($handle)) !== false) {
  if (!trim($line)) {
    continue;
  }
  list($amount, $source, $dest) = parseMove($line);
  for ($i = 0; $i < $amount; $i++) {
    $crate = $stacks[$source-1]->pop();
    $stacks[$dest-1]->push($crate);
  }
}

print 'Top crates: ';
foreach ($stacks as $stack) {
  print str_replace(['[', ']'], '', $stack->top());
}
print PHP_EOL;
