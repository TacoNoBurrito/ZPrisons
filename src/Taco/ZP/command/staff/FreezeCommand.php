<?php namespace Taco\ZP\command\staff;

use pocketmine\command\CommandSender;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;

class FreezeCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("freeze", "§rFreeze a Player (staff)", "", []);
        $this->setPermission("staff.freeze");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender)) {
            $player = array_shift($args);
            if ($player == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a player to freeze.");
                return;
            }
            if ($player = Core::getInstance()->getServer()->getPlayer($player)) {
                if ($player->isOp()) {
                    $sender->sendMessage(Core::getRandomPrefix()."You cannot freeze operators.");
                    return;
                }
                $session = Core::getSessionManager()->getPlayerSession($player);
                $session->setFrozenState(!$session->isFrozen());
                $nLfZn = ($session->isFrozen() ? "now" : "no longer");
                $sender->sendMessage(Core::getRandomPrefix().$player->getName()." is ".$nLfZn." frozen.");
                $player->sendMessage(Core::getRandomPrefix()."You are ".$nLfZn." frozen.");
            } else {
                $sender->sendMessage(Core::getRandomPrefix()."This player is not online, or doesn't exist.");
            }
        }
    }

}