<?php namespace Taco\ZP\gems;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\BlockIds;
use pocketmine\block\InvisibleBedrock;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\Pickaxe;
use pocketmine\Player;
use Taco\ZP\Core;
use Taco\ZP\gems\types\SellersGem;

class GemManager {

    private array $gems = [];

    public function init() : void {
        $this->gems = [
            "Sellers Gem" => new SellersGem()
        ];
    }

    public function getGem(string $gem) : ?Gem {
        if (!isset($this->gems[$gem])) return null;
        return $this->gems[$gem];
    }

    //TODO: touch this code when you have the audacity...
    public function openGemMenu(Player $player) : void {
        if (!$player->getInventory()->getItemInHand() instanceof Pickaxe) {
            $player->sendMessage(Core::getRandomPrefix()."You cant change your slot to something that isn't a pickaxe idiot.");
            return;
        }
        if (Core::getPickaxeUtils()->needsInit($player->getInventory()->getItemInHand())) {
            $player->sendMessage(Core::getRandomPrefix()."You cant change your slot to something that isn't initialized idiot.");
            return;
        }
        $menu = InvMenu::create(MenuIds::TYPE_CHEST);
        $menu->send($player);
        $menu->setName("Gems Menu");
        for ($i = 0; $i <= 26; $i++) {
            $menu->getInventory()->setItem($i, Item::get(BlockIds::INVISIBLE_BEDROCK)->setCustomName(""));
        }
        $pickaxe = $player->getInventory()->getItemInHand();
        $inv = $menu->getInventory();
        $inv->setItem(13, $pickaxe);
        $tag = $pickaxe->getNamedTag();
        $gem1 = $this->createGemFromString($tag->getString("gem1"));
        $gem2 = $this->createGemFromString($tag->getString("gem2"));
        $gem3 = $this->createGemFromString($tag->getString("gem3"));
        $gem4 = $this->createGemFromString($tag->getString("gem4"));
        $blank = Item::get(BlockIds::AIR);
        $inv->setItem(9, $gem1 == null ? $blank : $gem1->getItem());
        $inv->setItem(11, $gem2 == null ? $blank : $gem2->getItem());
        $inv->setItem(15, $gem3 == null ? $blank : $gem3->getItem());
        $inv->setItem(17, $gem4 == null ? $blank : $gem4->getItem());
        $menu->setListener(function(InvMenuTransaction $transaction) use($pickaxe, $gem1, $gem2, $gem3, $gem4) : InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            if ($item instanceof InvisibleBedrock) {
                return $transaction->discard();
            }
            if ($transaction->getIn()->getNamedTag()->hasTag("gemLevel")) return $transaction->continue();
            if ($transaction->getOut()->getNamedTag()->hasTag("gemLevel")) return $transaction->continue();
            return $transaction->discard();
        });
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use($pickaxe) : void {
            $gem1 = $inventory->getItem(9);
            $gem2 = $inventory->getItem(11);
            $gem3 = $inventory->getItem(15);
            $gem4 = $inventory->getItem(17);
            $pickaxe->getNamedTag()->setString("gem1", $gem1->getId() == 0 ? "" : $this->createStringFromGem($gem1));
            $pickaxe->getNamedTag()->setString("gem2", $gem2->getId() == 0 ? "" : $this->createStringFromGem($gem2));
            $pickaxe->getNamedTag()->setString("gem3", $gem3->getId() == 0 ? "" : $this->createStringFromGem($gem3));
            $pickaxe->getNamedTag()->setString("gem4", $gem4->getId() == 0 ? "" : $this->createStringFromGem($gem4));
            $player->getInventory()->setItemInHand(Core::getPickaxeUtils()->updateLore($pickaxe));
        });
    }

    public function createStringFromGem(Item $gem) : string {
        $tag = $gem->getNamedTag();
        //if (!$tag->hasTag("gemName"))
        return $tag->getString("gemName") . ":" . $tag->getInt("gemLevel") . ":" . $tag->getInt("gemType");
    }

    public function createGemFromString(string $gem) : ?Gem {
        if ($gem == "") return null;
        $exp = explode(":", $gem);
        $gType = $exp[0];
        $level = (int)$exp[1];
        $type = (int)$exp[2];
        if ($gType == "Sellers Gem") {
            return new SellersGem($level, $type);
        }
        return null;
    }

    public function getExtraMultiplier(Item $pickaxe) : int {
        if (Core::getPickaxeUtils()->needsInit($pickaxe)) return 1;
        $tag = $pickaxe->getNamedTag();
        $gem1 = $this->createGemFromString($tag->getString("gem1"));
        $gem2 = $this->createGemFromString($tag->getString("gem2"));
        $gem3 = $this->createGemFromString($tag->getString("gem3"));
        $gem4 = $this->createGemFromString($tag->getString("gem4"));
        $added = 1;
        if ($gem1 !== null and $gem1 instanceof SellersGem) $added += $gem1->getLevel();
        if ($gem2 !== null and $gem2 instanceof SellersGem) $added += $gem2->getLevel();
        if ($gem3 !== null and $gem3 instanceof SellersGem) $added += $gem3->getLevel();
        if ($gem4 !== null and $gem4 instanceof SellersGem) $added += $gem4->getLevel();
        return $added;
    }

}