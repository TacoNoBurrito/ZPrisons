<?php namespace Taco\ZP\ce\enchants\pickaxe;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\math\AxisAlignedBB;
use Taco\ZP\ce\enchants\pickaxe\task\BloomTask;
use Taco\ZP\ce\types\PickaxeEnchantment;
use Taco\ZP\Core;

class Bloom extends PickaxeEnchantment {

    public function onToggle(BlockBreakEvent $event, $mine) : void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $pointA = $mine->getPointA();
        $pointB = $mine->getPointB();
        $aabb = new AxisAlignedBB($pointA->getX(), $pointA->getY(), $pointA->getZ(), $pointB->getX(), $pointB->getY(), $pointB->getZ());
        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new BloomTask($block, $player, $aabb, $mine->getName()), 5);
    }

    public function getChance(int $level) : int {
        return 1000 - ($level * 10);
    }

    public function getListedRarity() : int {
        return Enchantment::RARITY_MYTHIC;
    }

    public function getListedName() : string {
        return "Bloom";
    }

    public function getDescription() : string {
        return "Create a upwards-blooming hole going up to 5x5!";
    }
}