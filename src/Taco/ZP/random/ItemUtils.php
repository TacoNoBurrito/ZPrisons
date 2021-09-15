<?php namespace Taco\ZP\util;

use pocketmine\entity\Effect;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use function explode;

class ItemUtils {

    public const TYPE_HEALTH = 0;
    public const TYPE_STRENGTH = 1;
    public const TYPE_JUMP = 2;

    //format as id:meta:ench1.enchlevel:ench1.enchlevel
    public static function convertStringItem(string $item) : Item {
        $exp = explode(":", $item);
        $id = (int)$exp[0];
        $meta = (int)$exp[1];
        unset($exp[0]);
        unset($exp[1]);
        $item = Item::get($id, $meta);
        foreach ($exp as $ench) {
            $expp = explode(".", $ench);
            $enchID = $expp[0];
            $enchLEVEL = $expp[1];
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchID), $enchLEVEL));
        }
        return $item;
    }

    public static function makeSoup(int $type) : Item {
        $item = Item::get(ItemIds::MUSHROOM_STEW);
        $types = ["§r§eHealth §7Soup", "§r§cStrength §7Soup", "§r§aJump §7Soup"];
        $item->setCustomName($types[$type]);
        $item->getNamedTag()->setString("isSoup", "true");
        $effects = ["6:2:3", "5:1:3", "8:3:6"];
        $item->getNamedTag()->setString("effect", $effects[$type]);
        return $item;
    }

}