<?php namespace Taco\ZP\command\gems;

use pocketmine\command\CommandSender;
use pocketmine\item\Pickaxe;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class GemMenuCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("gems", "§rOpen The Gems Menu.", "", []);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $item = $sender->getInventory()->getItemInHand();
            if ($item instanceof Pickaxe) {
                if (Core::getPickaxeUtils()->needsInit($item)) {
                    $sender->getInventory()->setItemInHand(Core::getPickaxeUtils()->initPickaxe($sender->getInventory()->getItemInHand()));
                    $sender->sendMessage(Core::getRandomPrefix()."Please try again.");
                    return;
                }
                Core::getGemManager()->openGemMenu($sender);
            } else {
                $sender->sendMessage(Core::getRandomPrefix()."This menu can only be used while holding a pickaxe.");
            }
            return;
        }
        $this->sendNoConsole($sender);
    }

}