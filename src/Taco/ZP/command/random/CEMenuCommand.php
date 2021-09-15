<?php namespace Taco\ZP\command\random;

use pocketmine\command\CommandSender;
use pocketmine\item\Pickaxe;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class CEMenuCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("cemenu", "§rOpen The Custom Enchant Upgrade Menu.", "", ["up", "upgrade"]);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $item = "";
            $hand = $sender->getInventory()->getItemInHand();
            if ($hand instanceof Pickaxe) $item = "pickaxe";
            if ($item == "") {
                $sender->sendMessage(Core::getRandomPrefix()."This item cannot be enchanted.");
                return;
            }
            Core::getCEManager()->openEnchantUI($sender, $item);
            return;
        }
        $this->sendNoConsole($sender);
    }

}