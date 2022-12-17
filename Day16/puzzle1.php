<?php

include_once './Dijkstra.php';

function parse($file) {
  $valves = [];

  $handle = fopen($file, 'r');
  while (($line = fgets($handle)) !== false && trim($line)) {
    list($valve, $tunnels) = explode('; ', trim($line));
    $rate =  substr($valve, 23);
    $valve = substr($valve, 6, 2);
    $tunnels = explode(', ', preg_replace('/tunnels? leads? to valves? /', '', $tunnels));
    $valves[$valve] = [
      'id' => $valve,
      'tunnels' => $tunnels,
      'rate' => $rate,
    ];
  }

  return $valves;
}

function solve($valves) {
  $distances = getDistances($valves);
  $valvesToOpen = array_filter($valves, function ($valve) {
    return $valve['id'] !== 'AA' && $valve['rate'] > 0;
  });
  return getOptimalPressureRelease($valvesToOpen, $distances, 'AA', 30);
}

function getDistances($valves) {
  $distances = [];
  $dijkstra = new Dijkstra(
    $valves,
    function() { return 1; },
    function ($item) use ($valves) {
      $neighbours = [];
      foreach ($item['tunnels'] as $neighbourId) {
        $neighbours[] = $valves[$neighbourId];
      }

      return $neighbours;
    }
  );

  foreach($valves as $valve) {
    $distances[$valve['id'] . '|' . $valve['id']] = 0;
    foreach ($valve['tunnels'] as $directNeighbour) {
      $distances[$valve['id'] . '|' . $directNeighbour] = 1;
      $distances[$directNeighbour . '|' . $valve['id']] = 1;
    }
    foreach ($valves as $neighbour) {
      if (!array_key_exists($valve['id'] . '|' . $neighbour['id'], $distances)) {
        $distances[$valve['id'] . '|' . $neighbour['id']] =
        $distances[$neighbour['id'] . '|' . $valve['id']] =
        $dijkstra->calculate($valve, $neighbour['id'])['_dijkstra']['distance'];
      }
    }
  }

  return $distances;
}

function getOptimalPressureRelease($valves, $distances, $currentValveId, $remainingMinutes = 30) {
  static $cache = [];

  $pressure = 0;
  ksort($valves);
  $cacheKey = $remainingMinutes . '|' . implode('_', array_keys($valves)) . '|' . $currentValveId;
  if (array_key_exists($cacheKey, $cache)) {
    return $cache[$cacheKey];
  }
  foreach ($valves as $valve) {
    $timeWaistedTravelling = $distances[$currentValveId . '|' . $valve['id']];
    $remainingMinutesAfterTravelling = $remainingMinutes - $timeWaistedTravelling;
    $remainingMinutesAfterOpening = $remainingMinutesAfterTravelling - 1;
    $newValves = $valves;
    unset($newValves[$valve['id']]);
    if ($remainingMinutesAfterOpening) {
      $pressureOption = ($remainingMinutesAfterOpening * $valve['rate']) + getOptimalPressureRelease($newValves, $distances, $valve['id'], $remainingMinutesAfterOpening);
      $pressure = $pressureOption > $pressure ? $pressureOption : $pressure;
    }
  }
  $cache[$cacheKey] = $pressure;
  return $cacheKey ? $cache[$cacheKey] : $pressure;
}

function getTimeWaisted($valve, $destinationValve, $distances) {
  return $distances[$valve['id'] . '|' . $destinationValve['id']] + 1;
}

// print solve(parse('sample.txt')) . PHP_EOL;
print solve(parse('input.txt')) . PHP_EOL;