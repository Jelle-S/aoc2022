<?php

class MonkeyInTheMiddle {
  /**
   * @var Monkey[]
   */
  protected $monkeys = [];

  protected int $product = 1;

  protected int $boredomFactor;

  /**
   *
   * @param Monkey[] $monkeys
   */
  public function __construct(array $monkeys, $boredomFactor = 3) {
    $this->boredomFactor = $boredomFactor;
    foreach ($monkeys as $monkey) {
      $this->monkeys[$monkey->getNumber()] = $monkey;
      $this->product *= $monkey->getTestDivisible();
    }
  }

  public function play(int $rounds = 20) {
    for ($i = 0; $i<$rounds; $i++) {
      $this->playRound();
    }
  }

  protected function playRound() {
    foreach ($this->monkeys as $monkey) {
      $this->playTurn($monkey);
    }
  }

  protected function playTurn(Monkey $monkey) {
    while ($item = $monkey->getNextItem()) {
      if ($this->boredomFactor !== 1) {
        $item = floor($monkey->inspectItem($item) / $this->boredomFactor);
      }
      else {
        $item = floor($monkey->inspectItem($item % $this->product));
      }
      $destination = $monkey->getDestination($item);
      $this->monkeys[$destination]->addItem($item);
    }
  }

  /**
   * @return Monkey[]
   */
  public function getMonkeysByActivity() {
    $monkeys = $this->monkeys;

    usort($monkeys, function (Monkey $a, Monkey $b) {
      return $b->getNumberOfInspectedItems() - $a->getNumberOfInspectedItems();
    });

    return $monkeys;
  }


}