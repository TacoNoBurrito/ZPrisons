<?php namespace Taco\ZP\command\random;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class SpawnCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("spawn", "§rWarp Back To Spawn!", "", []);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $sender->sendMessage(Core::getRandomPrefix()."You have teleported to spawn.");
            $pos = $sender->getPosition();
            Core::getEntityUtils()->spawnEntity("TeleportText", $pos->getX() + 0.5 . ":" . ($pos->getY() + 1) . (":" . ($pos->getZ() + 0.5)), $sender->getLevel(), "§bWarped To Spawn...");
            $sender->teleport(Core::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
            return;
        }
        $this->sendNoConsole($sender);
    }

}