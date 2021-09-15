<?php namespace Taco\ZP\ce\enchants\pickaxe;

use pocketmine\block\Bedrock;
use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\enchantment\Enchantment;
use Taco\ZP\ce\types\PickaxeEnchantment;
use Taco\ZP\Core;

class Strike extends PickaxeEnchantment {


    public function getListedName() : string {
        return "Strike";
    }

    public function getListedRarity() : int {
        return Enchantment::RARITY_RARE;
    }

    public function getDescription() : string {
        return "Send a lightning strike so hard it goes to bedrock!";
    }

    public function onToggle(BlockBreakEvent $event, $mine) : void {
        $block = $event->getBlock();
        $nb = $block->getLevel()->getBlock($block);
        $timesRan = 1;
        $money = 0;
        Core::getEntityUtils()->spawnLightning($block);
        $sellPrices = Core::getInstance()->config["sell-prices"];
        $lowY = $mine->getPointA()->getY() < $mine->getPointB()->getY() ? $mine->getPointA()->getY() : $mine->getPointB()->getY();
        while($nb->getY() > $lowY) {
            $nb = $block->getLevel()->getBlock($block->subtract(0, $timesRan));
            if ($nb instanceof Bedrock) break;
            if (isset($sellPrices[$nb->getId().":".$nb->getDamage()])) {
                $money += $sellPrices[$nb->getId().":".$nb->getDamage()];
                Core::getInstance()->blocksInMine[$mine->getName()] -= 1;
                if ((Core::getInstance()->blocksInMine[$mine->getName()] - 10) < 50) {
                    Core::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "mine reset ".$mine->getName());
                    Core::getInstance()->blocksInMine[$mine->getName()] = Core::getInstance()->storedBlocksInMine[$mine->getName()];
                }
            }
            $block->getLevel()->setBlock($nb, Block::get(0));
            $timesRan++;
        }
        $player = $event->getPlayer();
        $player->sendMessage("§l§6Strike §r§7> §fStrike made §a$".$money."§f!");
        Core::getSessionManager()->getPlayerSession($player)->addMoney($money);
    }

    public function getChance(int $level) : int {
        return 500 - ($level * 5);
    }
}