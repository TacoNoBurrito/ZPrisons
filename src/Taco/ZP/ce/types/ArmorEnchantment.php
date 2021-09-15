<?php namespace Taco\ZP\ce\types;

use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\item\Item;
use pocketmine\Player;

abstract class ArmorEnchantment extends BaseEnchant {

    abstract function onToggle(Player $player, Item $item) : void;

    abstract function onUnEquip(Player $player, Item $item) : void;

    public static function toggle(EntityArmorChangeEvent $event) : void {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $new = $event->getNewItem();
            $old = $event->getOldItem();
            foreach ($new->getEnchantments() as $enchantment) {
                $enchantment = $enchantment->getType();
                if ($enchantment instanceof ArmorEnchantment) {
                    if (!$new->equals($old, false, true)) {
                        $enchantment->onToggle($player, $new);
                    }
                }
            }
            foreach ($old->getEnchantments() as $enchantment) {
                $enchantment = $enchantment->getType();
                if ($enchantment instanceof ArmorEnchantment) {
                    if (!$old->equals($new, false, true)) {
                        $enchantment->onUnEquip($player, $old);
                    }
                }
            }
        }
    }

}