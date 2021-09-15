<?php namespace Taco\ZP\command\gems;

use pocketmine\command\CommandSender;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use Taco\ZP\gems\Gem;
use function array_shift;
use function is_numeric;

class GiveGemCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("givegem", "§rGive a Gem To a Player (staff)", "", []);
        $this->setPermission("staff.givegem");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender)) {
            $player = array_shift($args);
            if ($player == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a player to give a gem to.");
                return;
            }
            $player = Core::getInstance()->getServer()->getPlayer($player);
            if ($player == null) {
                $sender->sendMessage(Core::getRandomPrefix()."This player is not online or doesn't exist.");
                return;
            }
            $gem = array_shift($args);
            if ($gem == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a gem to give to the player.");
                return;
            }
            $strGem = $gem;
            $gem = Core::getGemManager()->getGem($gem);
            if (!$gem) {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a valid gem.");
                return;
            }
            $level = array_shift($args);
            if ($level == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a level for the gem!");
                return;
            }
            if (!is_numeric((int)$level)) {
                $sender->sendMessage(Core::getRandomPrefix()."The level must be a number!");
                return;
            }
            $map = [
                "Sellers Gem" => Gem::MODIFIER_SELL_BOOST
            ];
            $gem = new $gem($level, $map[$strGem]);
            $sender->sendMessage(Core::getRandomPrefix()."Successfully gave a ".$gem->getName()." gem to ".$player->getName()."!");
            $player->sendMessage(Core::getRandomPrefix()."You have received a ".$gem->getName()." gem.");
            $player->getInventory()->canAddItem($gem->getItem()) ? $player->getInventory()->addItem($gem->getItem()) : $player->getLevel()->dropItem($player->getPosition(), $gem->getItem());
            return;
        }
        $sender->sendMessage(EasyCommand::NO_PERMISSION);
    }

}