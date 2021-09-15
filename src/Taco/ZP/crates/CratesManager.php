<?php namespace Taco\ZP\crates;

use pocketmine\block\BlockIds;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Taco\ZP\Core;
use function explode;
use function mt_rand;

class CratesManager {

    /**
     * @var array<string, CrateReward>
     */
    private array $crateRewards = [];


    public function init() : void {
        foreach (Core::getInstance()->config["crates"] as $crate) {
            $crate = explode(":", $crate);
            $pos = new Vector3((int)$crate[0], (int)$crate[1], (int)$crate[2]);
            $level = Core::getInstance()->getServer()->getLevelByName($crate[3]);
            $coloredName = $crate[5];
            $level->addParticle(new FloatingTextParticle($pos->add(0.5, 2, 0.5), $coloredName."§r§f Crate\n§fTap this crate with a ".$coloredName." §r§7key\n§7to receive rewards!\n\n§r§o§eBuy More Keys at ZPrisons.Tebex.IO", " "));
        }
        $this->crateRewards = [
            "Vote" => [
                new CrateReward(true, null, "givemoney {player} 100000"),
                new CrateReward(true, null, "givegem {player} \"Sellers Gem\"")
            ]
        ];
    }

    public function openCrate(Player $player, string $type) : void {
        /*** @var CrateReward $reward */
        foreach ($this->crateRewards[$type] as $reward) {
            if (mt_rand(1, 2) == 2) {
                if ($reward->isCommand()) {
                    Core::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), $reward->getCommand($player));
                } else {
                    $item = $reward->getItem();
                    if ($player->getInventory()->canAddItem($item)) $player->getInventory()->addItem($item);
                    else $player->getLevel()->dropItem($player->getPosition(), $item);
                }
                $player->sendMessage(Core::getRandomPrefix()."§eYou have received your crate reward!");
                return;
            }
        }
    }

    public function addCrate(string $name, string $pos, Level $level, string $fancyName) : void {
        Core::getInstance()->config["crates"][] = $pos.":".$level->getName().":".$name.":".$fancyName;
    }

    public function getCrateAtPosition(Position $pos) : ?string {
        $str = $pos->getX().":".$pos->getY().":".$pos->getZ();
        foreach (Core::getInstance()->config["crates"] as $crate) {
            $crate = explode(":", $crate);
            if ($str == $crate[0].":".$crate[1].":".$crate[2]) {
                return $crate[4];
            }
        }
        return null;
    }

    public function giveKey(Player $player, string $type) : void {
        $key = Item::get(BlockIds::TRIPWIRE_HOOK);
        $coolName = "";
        foreach (Core::getInstance()->config["crates"] as $crate) {
            $crate = explode(":", $crate);
            if ($crate[4] == $type) {
                $coolName = $crate[5];
            }
        }
        $key->setCustomName($coolName." §r§fKey");
        $key->setLore(["§r§7------------------------------------------------------\n§fUse This Key On a $coolName §r§fCrate In Spawn!\n§r§7------------------------------------------------------"]);
        $key->getNamedTag()->setString("crateType", $type);
        $player->getInventory()->canAddItem($key) ? $player->getInventory()->addItem($key) : $player->getLevel()->dropItem($player->getPosition(), $key);
        $player->sendMessage(Core::getRandomPrefix()."§l§eYou have received a §r$coolName §r§l§ekey!");
    }

}