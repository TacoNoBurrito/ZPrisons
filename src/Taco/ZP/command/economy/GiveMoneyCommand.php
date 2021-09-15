<?php namespace Taco\ZP\command\economy;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;
use function is_numeric;

class GiveMoneyCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("givemoney", "§rGive Money To a Player", "", []);
        $this->setPermission("staff.givemoney");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender) or !$sender instanceof Player) {
            $player = array_shift($args);
            if ($player == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a valid player to give money to.");
                return;
            }
            $player = Core::getInstance()->getServer()->getPlayer($player);
            if ($player == null) {
                $sender->sendMessage(Core::getRandomPrefix()."This player is not online or doesn't exist.");
                return;
            }
            $amount = array_shift($args);
            if ($amount == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a amount to give to the player.");
                return;
            }
            if (!is_numeric((int)$amount)) {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a numeric amount to give to the player.");
                return;
            }
            Core::getSessionManager()->getPlayerSession($player)->addMoney($amount);
            $sender->sendMessage(Core::getRandomPrefix()."Successfully gave §a$".$amount."§f to §e".$player->getName()."§f.");
            $player->sendMessage(Core::getRandomPrefix()."You have received §a$".$amount."§f!");
            return;
        }
        $sender->sendMessage(EasyCommand::NO_PERMISSION);
    }

}