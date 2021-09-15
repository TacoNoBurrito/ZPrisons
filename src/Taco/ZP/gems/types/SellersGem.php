<?php namespace Taco\ZP\gems\types;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use Taco\ZP\Core;
use Taco\ZP\gems\Gem;
use function wordwrap;

class SellersGem extends Gem {

    public function __construct(int $level = 0, int $type = 0) {
        parent::__construct($level, $type);
    }

    public function getItem(): Item {
        $item = Item::get(ItemIds::EMERALD);
        $item->setCustomName("§r§l§aSELLERS GEM §r§7[§l§e".Core::getPickaxeUtils()->ITR($this->getLevel())."§r§7]");
        $item->setLore([
            "§r§bLevel§7: §d".$this->getLevel(),
            "§r§bDescription:",
            "§r§7 - §e".wordwrap("This gem will multiply your sell booster§d x".$this->getLevel()."§e!")
        ]);
        $item->getNamedTag()->setInt("gemLevel", $this->getLevel());
        $item->getNamedTag()->setInt("gemType", $this->getType());
        $item->getNamedTag()->setString("gemName", $this->getName());
        return $item;
    }

    public function getName() : string {
        return "Sellers Gem";
    }


}