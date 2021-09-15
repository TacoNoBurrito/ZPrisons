<?php namespace Taco\ZP\command\random;

use pocketmine\command\CommandSender;
use pocketmine\item\Pickaxe;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class KitCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("kits", "§rOpen The Kits Menu!", "", ["kit"]);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            Core::getKitManager()->openKitListForm($sender);
            return;
        }
        $this->sendNoConsole($sender);
    }

}