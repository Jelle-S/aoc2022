<?php

class Monkey {
  const OPERATIONTYPE_MULTIPLY = '*';
  const OPERATIONTYPE_ADD = '+';
  const REGEX = '/Monkey (\d+):\n\s+Starting items: (((\d)+(, )?)+)\n\s+Operation: new = (\d+|old) (\*|\+) (\d+|old)\n\s+Test: divisible by (\d+)\n\s+If true: throw to monkey (\d+)\n\s+If false: throw to monkey (\d+)/';

  protected int $number;
  protected string $operationType;
  protected $operationValue;
  protected SplQueue $items;
  protected int $testDivisible;
  protected array $destinationMonkeys;
  protected int $numberOfInspectedItems = 0;

  public function __construct(int $number, string $operationType, $operationValue, array $items, int $testDivisible, array $destinationMonkeys) {
    $this->number = $number;
    $this->operationType = $operationType;
    $this->operationValue = $operationValue;
    $this->items = new SplQueue();
    foreach ($items as $item) {
      $this->addItem($item);
    }
    $this->testDivisible = $testDivisible;
    $this->destinationMonkeys = $destinationMonkeys;
  }

  public static function fromDescription($description): static {
    $matches = [];
    preg_match(static::REGEX, $description, $matches);
    return new static(
      (int) $matches[1],
      $matches[7],
      $matches[8],
      array_map('intval', explode(', ', $matches[2])),
      (int) $matches[9],
      [0 => (int) $matches[11], 1 => (int) $matches[10]]
    );
  }

  public function addItem(int $item) {
    $this->items->enqueue($item);
  }

  public function getNextItem() {
    if (!$this->items->isEmpty()) {
      return $this->items->dequeue();
    }

    return false;
  }

  public function getNumber(): int {
    return $this->number;
  }

  public function getNumberOfInspectedItems(): int {
    return $this->numberOfInspectedItems;
  }

  public function getTestDivisible(): int {
    return $this->testDivisible;
  }

  public function inspectItem(int $item) {
    $this->numberOfInspectedItems++;
    $operationValue = $this->operationValue === 'old' ? $item : $this->operationValue;
    switch ($this->operationType) {
      case static::OPERATIONTYPE_ADD:
        return $item + $operationValue;
      case static::OPERATIONTYPE_MULTIPLY:
        return $item * $operationValue;
    }
  }

  public function getDestination(int $item) {
    return $this->destinationMonkeys[(int)(($item % $this->testDivisible) === 0)];
  }
}
