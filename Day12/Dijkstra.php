<?php

class Dijkstra {

  protected array $grid;

  protected Closure $distanceCalculator;

  protected Closure $neighbourCoordinatesCalculator;

  protected bool $storePath = false;

  protected bool $storeSteps = false;

  public function __construct(array $grid, Closure $distanceCalculator) {
    $this->grid = $grid;
    $this->distanceCalculator = $distanceCalculator;
    $this->neighbourCoordinatesCalculator = function ($item) {
      return [
        ['x' => $item['x'] - 1, 'y' => $item['y']],
        ['x' => $item['x'] + 1, 'y' => $item['y']],
        ['x' => $item['x'], 'y' => $item['y'] - 1],
        ['x' => $item['x'], 'y' => $item['y'] + 1],
      ];
    };
  }

  public function shouldStorePath($storePath = true) {
    $this->storePath = $storePath;
  }

  public function shouldStoreSteps($storeSteps = true) {
    $this->storeSteps = $storeSteps;
  }

  public function setNeighbourCoordinatesCalculator(Closure $neighbourCoordinatesCalculator) {
    $this->neighbourCoordinatesCalculator = $neighbourCoordinatesCalculator;
  }

  public function calculate($start, $destination) {
    $queue = new SplPriorityQueue();
    $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

    $start['distance'] = 0;
    if ($this->storePath) {
      $start['pathToItem'] = [];
    }
    if ($this->storeSteps) {
      $start['stepsToItem'] = [];
    }
    $queue->insert($start, 0);
    $visited = [$start['y'] => [$start['x'] => true]];

    while (!$queue->isEmpty()) {
      $queueItem = $queue->extract();
      $item = $queueItem['data'];
      $priority = $queueItem['priority'];
      $neighboursCoordinates = ($this->neighbourCoordinatesCalculator)($item);
      foreach ($neighboursCoordinates as $neighboursCoordinate) {
        $x = $neighboursCoordinate['x'];
        $y = $neighboursCoordinate['y'];
        if (!isset($this->grid[$y][$x])) {
          continue;
        }
        if (isset($visited[$y][$x])) {
          continue;
        }
        $neighbour = ['x' => $x, 'y' => $y, 'data' => $this->grid[$y][$x]];
        $distance = ($this->distanceCalculator)($item, $neighbour);
        if ($distance === false) {
          // Neighbour is not reachable according to the distance calculator.
          continue;
        }
        $visited[$y][$x] = true;
        $neighbour['distance'] = $item['distance'] + $distance;

        if ($this->storePath) {
          $neighbour['pathToItem'] = array_merge($item['pathToItem'], ['x' => $neighbour['x'], 'y' => $neighbour['y']]);
        }
        if ($this->storeSteps) {
          $neighbour['stepsToItem'] = array_merge($item['stepsToItem'], ['x' => $item['x'] - $neighbour['x'], 'y' => $item['y'] - $neighbour['y']]);
        }

        if ($this->matchesDestination($neighbour, $destination)) {
          return $neighbour;
        }

        $queue->insert($neighbour, -$neighbour['distance']);
      }
    }

    // No path found.
    return null;
  }

  protected function matchesDestination($item, $destination) {
    if (is_callable($destination)) {
      return $destination($item);
    }

    return $item['x'] === $destination['x'] && $item['y'] === $destination['y'];
  }

}
