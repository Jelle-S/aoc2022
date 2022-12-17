<?php

class Dijkstra {

  protected array $items;

  protected Closure $distanceCalculator;

  protected Closure $neighbourCalculator;

  protected bool $storePath = false;

  public function __construct(array $items, Closure $distanceCalculator, Closure $neighbourCalculator) {
    $this->items = $items;
    $this->distanceCalculator = $distanceCalculator;
    $this->neighbourCalculator = $neighbourCalculator;
  }

  public function shouldStorePath($storePath = true) {
    $this->storePath = $storePath;
  }

  public function calculate(array $start, $destination) {
    $queue = new SplPriorityQueue();
    $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    $start['_dijkstra'] = [];
    $start['_dijkstra']['distance'] = 0;
    if ($this->storePath) {
      $start['_dijkstra']['pathToItem'] = [];
    }
    $queue->insert($start, 0);
    $visited = [$start['id'] => true];

    while (!$queue->isEmpty()) {
      $queueItem = $queue->extract();
      $item = $queueItem['data'];
      $priority = $queueItem['priority'];
      $neighbours = ($this->neighbourCalculator)($item);
      foreach ($neighbours as $neighbour) {
        if (isset($visited[$neighbour['id']])) {
          continue;
        }
        $distance = ($this->distanceCalculator)($item, $neighbour);
        if ($distance === false) {
          // Neighbour is not reachable according to the distance calculator.
          continue;
        }
        $visited[$neighbour['id']] = true;
        $neighbour['_dijkstra']['distance'] = $item['_dijkstra']['distance'] + $distance;

        if ($this->storePath) {
          $neighbour['_dijkstra']['pathToItem'] = array_merge($item['_dijkstra']['pathToItem'], [$neighbour['id']]);
        }

        if ($this->matchesDestination($neighbour, $destination)) {
          return $neighbour;
        }

        $queue->insert($neighbour, -$neighbour['_dijkstra']['distance']);
      }
    }

    // No path found.
    return null;
  }

  protected function matchesDestination(array $item, $destination) {
    if (is_callable($destination)) {
      return $destination($item);
    }

    return $item['id'] === $destination;
  }

}
