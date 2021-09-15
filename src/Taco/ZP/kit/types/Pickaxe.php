<?php namespace Taco\ZP\kit\types;

use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use Taco\ZP\Core;
use Taco\ZP\kit\KitType;
use Taco\ZP\random\TimeUtils;
use Taco\ZP\util\ItemUtils;

class Pickaxe extends KitType {

    public function getCoolItemNames() : string {
        return "§r§ePickaxe";
    }

    public function getName() : string {
        return "Pickaxe";
    }

    public function getItems() : array {
        $pickaxe = Item::get(ItemIds::DIAMOND_PICKAXE);
        $pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 10));
        if ($pickaxe instanceof Durable) $pickaxe->setUnbreakable(true);
        Core::getPickaxeUtils()->initPickaxe($pickaxe);
        $items = [$pickaxe];
        $newItems = [];
        foreach ($items as $item) {
            $newItems[] = $item->setCustomName($this->getCoolItemNames()."§r§f ".$item->getName());
        }
        return $newItems;
    }

    public function getPermission() : string {
        return "none";
    }

    public function getKitCooldown() : int {
        return TimeUtils::TIME_HOUR;
    }

}