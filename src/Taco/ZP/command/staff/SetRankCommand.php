<?php namespace Taco\ZP\command\staff;

use pocketmine\command\CommandSender;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;

class SetRankCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("setrank", "§rSet a Players Rank (staff)", "", []);
        $this->setPermission("staff.setrank");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender)) {
            $player = array_shift($args);
            if ($player == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a player that you will change the rank that they have. i cant english please help");
                return;
            }
            if ($player = Core::getInstance()->getServer()->getPlayer($player)) {
                $rank = array_shift($args);
                if ($rank == "") {
                    $sender->sendMessage(Core::getRandomPrefix()."Please provide a rank to give to the player.");
                    return;
                }
                Core::getSessionManager()->getPlayerSession($player)->setRank($rank);
                $sender->sendMessage(Core::getRandomPrefix()."Successfully set ".$player->getName()."'s rank to ".$rank);
            } else {
                $sender->sendMessage(Core::getRandomPrefix()."This player is not online, or doesn't exist.");
            }
        }
    }

}