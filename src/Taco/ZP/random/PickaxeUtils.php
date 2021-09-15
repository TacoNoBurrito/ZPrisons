<?php namespace Taco\ZP\random;

use pocketmine\item\Item;
use Taco\ZP\Core;
use function explode;
use function in_array;

class PickaxeUtils {

    public function needsInit(Item $pickaxe) : bool {
        return !$pickaxe->getNamedTag()->hasTag("blocks");
    }

    public function initPickaxe(Item $pickaxe) : Item {
        $pickaxe->getNamedTag()->setString("gem1", "");
        $pickaxe->getNamedTag()->setString("gem2", "");
        $pickaxe->getNamedTag()->setString("gem3", "");
        $pickaxe->getNamedTag()->setString("gem4", "");
        $pickaxe->getNamedTag()->setInt("blocks", 0);
        $pickaxe->setLore([
            "§r§l§eCustom Enchants: ",
            "§r§7 - §fNone",
            "§r§l§eGems: ",
            " §r§fGem Slot 1: §7Empty",
            " §r§fGem Slot 2: §7Empty",
            " §r§fGem Slot 3: §7Empty",
            " §r§fGem Slot 4: §7Empty",
            "§r§l§eBlocks Broken:",
            " §r§7- §f0"
        ]);
        $pickaxe = $this->updateLore($pickaxe);
        return $pickaxe;
    }

    public function updateLore(Item $pickaxe) : Item {
        $tag = $pickaxe->getNamedTag();
        $gems = [
            $tag->getString("gem1"),
            $tag->getString("gem2"),
            $tag->getString("gem3"),
            $tag->getString("gem4")
        ];
        $enchants = $pickaxe->getEnchantments();
        $blocks = $tag->getInt("blocks");
        $newLore = ["§r§l§eCustom Enchants: "];
        foreach ($enchants as $ench) {
            if (isset(Core::getCEManager()->enchantments[$ench->getId()])) {
                $newLore[] = "§r§7 - §e".$ench->getType()->getName()." §7[§l§6".$this->ITR($ench->getLevel())."§r§7]";
            }
        }
        $newLore[] = "§r§l§eGems: ";
        $current = 0;
        foreach ($gems as $gem) {
            $current++;
            if ($current > 4) break;
            $newLore[] = " §r§fGem Slot $current: ".($gem == "" ? "§7Empty" : Core::getGemManager()->createGemFromString($gem)->getItem()->getCustomName());
        }
        $newLore[] = "§r§l§eBlocks Broken:";
        $newLore[] = " §r§7- §f".$blocks;
        return $pickaxe->setLore($newLore);
    }

    public function ITR(int $level) : string {
        $romanNumeralConversionTable = [
            "X"  => 10,
            "IX" => 9,
            "V"  => 5,
            "IV" => 4,
            "I"  => 1
        ];
        $romanString = "";
        while($level > 0){
            foreach ($romanNumeralConversionTable as $rom => $arb){
                if ($level >= $arb){
                    $level -= $arb;
                    $romanString .= $rom;
                    break;
                }
            }
        }
        return $romanString;
    }

}