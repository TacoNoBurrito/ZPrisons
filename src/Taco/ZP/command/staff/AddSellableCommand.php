<?php namespace Taco\ZP\command\staff;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;
use function is_numeric;

class AddSellableCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("addsellable", "§rAdd a Sellable Block (staff)", "", []);
        $this->setPermission("staff.addsellable");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            if ($this->testPermission($sender)) {
                $item = array_shift($args);
                if ($item == "") {
                    $sender->sendMessage(Core::getRandomPrefix()."Please provide a item id and meta to add. ex: §e1:0");
                    return;
                }
                $price = array_shift($args);
                if ($price == "") {
                    $sender->sendMessage(Core::getRandomPrefix()."Please provide a price that the item will sell for.");
                    return;
                }
                if (!is_numeric((int)$price)) {
                    $sender->sendMessage(Core::getRandomPrefix()."The price it sells for must be a number.");
                    return;
                }
                $sender->sendMessage(Core::getRandomPrefix()."Successfully added ".$item." to the sellable list for $".$price."!");
                Core::getInstance()->config["sell-prices"][$item] = $price;
                return;
            }
            $sender->sendMessage(EasyCommand::NO_PERMISSION);
            return;
        }
        $this->sendNoConsole($sender);
    }

}