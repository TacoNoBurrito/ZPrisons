<?php namespace Taco\ZP\command\random;

use pocketmine\command\CommandSender;
use pocketmine\item\Pickaxe;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function strtoupper;

class MineCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("mtp", "§rTeleport To Your Mine!", "");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $mine = strtoupper(Core::getSessionManager()->getPlayerSession($sender)->getMine());
            if (!Core::getInstance()->getServer()->isLevelGenerated($mine) or !Core::getInstance()->getServer()->isLevelLoaded($mine)) {
                $sender->sendMessage(Core::getRandomPrefix()."This mine is closed for repairs.");
                return;
            }
            $sender->teleport(Core::getInstance()->getServer()->getLevelByName($mine)->getSafeSpawn());
            $sender->sendMessage(Core::getRandomPrefix()."Successfully teleported to mine §e".$mine."§f!");
            return;
        }
        $this->sendNoConsole($sender);
    }

}