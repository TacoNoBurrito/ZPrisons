<?php namespace Taco\ZP\ce\types;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use function mt_rand;
use function var_dump;

abstract class PickaxeEnchantment extends BaseEnchant {

    abstract function onToggle(BlockBreakEvent $event, $mine) : void;

    abstract function getChance(int $level) : int;

    public static function toggle(BlockBreakEvent $event, Item $item, $mine) : void {
        if ($item->getId() == ItemIds::DIAMOND_PICKAXE) {
            foreach ($item->getEnchantments() as $ench) {
                $level = $ench->getLevel();
                $ench = $ench->getType();
                if ($ench instanceof PickaxeEnchantment) {
                    if (mt_rand(0, $ench->getChance($level)) < 2 or ($event->getPlayer()->isOp() and $event->getPlayer()->isSneaking())) {
                        $ench->onToggle($event, $mine);
                    }
                }
            }
        }
    }

}