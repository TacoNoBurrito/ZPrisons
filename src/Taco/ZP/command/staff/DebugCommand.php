<?php namespace Taco\ZP\command\staff;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;

class DebugCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("debug", "§rA Command For The Developer (dev-only)", "", []);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender->getName() == "TTqco") {
            $arg = array_shift($args);
            if ($arg == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a argument.");
                return;
            }
            if ($sender instanceof Player) {
                switch ($arg) {
                    case "initPickaxe":
                        $sender->getInventory()->setItemInHand(Core::getPickaxeUtils()->initPickaxe($sender->getInventory()->getItemInHand()));
                        $sender->sendMessage(Core::getRandomPrefix() . "Success.");
                        break;
                }
            }
            return;
        }
        $sender->sendMessage(Core::getRandomPrefix()."This command is for TTqco only. smh");
    }

}