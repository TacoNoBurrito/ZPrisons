<?php namespace Taco\ZP\gems;

use pocketmine\item\Item;

abstract class Gem {

    private int $level;

    private int $type;

    public const MODIFIER_SELL_BOOST = 0;

    public function __construct(int $level = 1, int $type = 0) {
        $this->level = $level;
        $this->type = $type;
    }

    public function getLevel() : int {
        return $this->level;
    }

    public function getType() : int {
        return $this->type;
    }

    abstract function getItem() : Item;

    abstract function getName() : string;

}