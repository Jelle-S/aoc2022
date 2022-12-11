<?php

class MonkeyInTheMiddle {
  /**
   * @var Monkey[]
   */
  protected $monkeys = [];

  /**
   *
   * @param Monkey[] $monkeys
   */
  public function __construct(array $monkeys) {
    foreach ($monkeys as $monkey) {
      $this->monkeys[$monkey->getNumber()] = $monkey;
    }
  }

  public function play(int $rounds = 20) {
    for ($i = 0; $i<$rounds; $i++) {
      $this->playRound();
    }
  }

  protected function playRound() {
    foreach ($this->monkeys as $monkey) {
      while ($itemWithDestination = $monkey->inspectAndThrowItem()) {
        $this->monkeys[$itemWithDestination['destination']]->addItem($itemWithDestination['item']);
      }
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