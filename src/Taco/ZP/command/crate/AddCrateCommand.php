<?php namespace Taco\ZP\command\crate;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;

class AddCrateCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("addcrate", "§rAdd a Crate (staff)");
        $this->setPermission("staff.addcrate");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender) and $sender instanceof Player) {
            $type = array_shift($args);
            if ($type == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a name for the crate.");
                return;
            }
            $fancyName = array_shift($args);
            if ($fancyName == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a colored name for the crate.");
                return;
            }
            $pos = $sender->getPosition();
            Core::getCratesManager()->addCrate($type, $pos->getFloorX().":".$pos->getFloorY().":".$pos->getFloorZ(),$sender->getLevel(), $fancyName);
            $sender->sendMessage(Core::getRandomPrefix()."Successfully added crate!");
            return;
        }
        $sender->sendMessage(EasyCommand::NO_PERMISSION);
    }

}