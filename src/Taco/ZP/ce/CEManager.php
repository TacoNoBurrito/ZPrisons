<?php namespace Taco\ZP\ce;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\BlockIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Taco\ZP\ce\enchants\pickaxe\Bloom;
use Taco\ZP\ce\enchants\pickaxe\Strike;
use Taco\ZP\Core;

class CEManager {

    public array $enchantments = [
        80 => "Bloom",
        81 => "Strike"
    ];

    public function init() : void {
        Enchantment::registerEnchantment(new Bloom(80, "Bloom", Enchantment::RARITY_MYTHIC, 0x0, 0x0, 25));
        Enchantment::registerEnchantment(new Strike(81, "Strike", Enchantment::RARITY_RARE, 0x0, 0x0, 25));
    }

    public function addEnchantmentToPickaxe(Item $pickaxe, int $id, int $level) : Item {
        $pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $level));
        return Core::getPickaxeUtils()->updateLore($pickaxe);
    }

    public function addEnchantment(Item $item, int $id, int $level) : Item {
        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $level));
        $newLore = [
            "§r§l§eCustom Enchants: "
        ];
        foreach ($item->getEnchantments() as $ench) {
            if (isset($this->enchantments[$ench->getId()])) {
                $newLore[] = "§r§7 - §r§e".$ench->getType()->getName()." §7[§l§6".Core::getPickaxeUtils()->ITR($ench->getLevel())."§r§7]";
            }
        }
        return $item->setLore($newLore);
    }

    public function openEnchantUI(Player $player, string $type) : void {
        $menu = InvMenu::create(MenuIds::TYPE_CHEST);
        $menu->send($player, "Custom Enchant Menu");
        $inv = $menu->getInventory();
        $enchants = [//13 for pickaxe
            "pickaxe" => [
                80 => [
                    "item" => BlockIds::RED_FLOWER.":0",
                    "price" => 100000,
                    "name" => Enchantment::getEnchantment(80)->getName()
                ],
                81 => [
                    "item" => BlockIds::PORTAL.":0",
                    "price" => 75000,
                    "name" => Enchantment::getEnchantment(81)->getName()
                ]
            ]
        ];
        $handItem = $player->getInventory()->getItemInHand();
        $inv->setItem(13, $handItem);
        $slot = 0;
        foreach($enchants[$type] as $id => $info) {
            $ar = explode(":", $info["item"]);
            $item = Item::get((int)$ar[0], (int)$ar[1]);
            $item->setCustomName(TextFormat::RESET.$info["name"]);
            $item->setLore(["§r§7Price §a$".Core::getNumberUtils()->intToPrefix($info["price"])."\n\n§r§7Current Level §a".($handItem->hasEnchantment($id) ? $handItem->getEnchantmentLevel($id) : 0)."\n\n§r§eTap Me To Upgrade!"]);
            $item->getNamedTag()->setInt("id", $id);
            $item->getNamedTag()->setInt("price", $info["price"]);
            if ($slot == 13) $slot++;
            $inv->setItem($slot, $item);
            $slot++;
        }
        $money = Core::getSessionManager()->getPlayerSession($player)->getMoney();
        $menu->setListener(function(InvMenuTransaction $transaction) use ($money, $handItem, $type) : InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            if ($item->getNamedTag()->hasTag("id")) {
                $price = $item->getNamedTag()->getInt("price");
                if ($money >= $price) {
                    $handItemForPlayer = $player->getInventory()->getItemInHand();
                    if (!$handItemForPlayer->equals($handItem, false, true)) {
                        $player->sendMessage(Core::getRandomPrefix()."Dupe detected.");
                        $transaction->getPlayer()->removeWindow($transaction->getAction()->getInventory());
                        return $transaction->discard();
                    }
                    Core::getSessionManager()->getPlayerSession($player)->takeMoney($price);
                    $id = $item->getNamedTag()->getInt("id");
                    $level = $handItem->hasEnchantment($id) ? $handItem->getEnchantmentLevel($id) + 1 : 1;
                    $handItem->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $level));
                    $player->getInventory()->setItemInHand(Core::getPickaxeUtils()->updateLore($handItem));
                    $transaction->getPlayer()->removeWindow($transaction->getAction()->getInventory());
                    $this->openEnchantUI($player, $type);
                }
                return $transaction->discard();
            }
            return $transaction->continue();
        });
    }

}