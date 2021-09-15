<?php namespace Taco\ZP\ce;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;
use function is_numeric;

class CECommand extends EasyCommand {

    public function __construct() {
        parent::__construct("ce", "§rBase Custom Enchants (op)");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if (!$sender instanceof Player or $sender->isOp()) {
            $player = array_shift($args);
            if ($player == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a player to enchant.");
                return;
            }
            $player = Core::getInstance()->getServer()->getPlayer($player);
            if ($player == null) {
                $sender->sendMessage(Core::getRandomPrefix()."This player is not online or doesn't exist.");
                return;
            }
            $id = array_shift($args);
            if ($id == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a enchant id.");
                return;
            }
            if (!is_numeric((int)$id)) {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a valid id.");
                return;
            }
            if (!isset(Core::getCEManager()->enchantments[$id])) {
                $sender->sendMessage(Core::getRandomPrefix()."That is not a valid id.");
                return;
            }
            $level = array_shift($args);
            if ($level == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide level for the enchant.");
                return;
            }
            if (!is_numeric((int)$level)) {
                $sender->sendMessage(Core::getRandomPrefix()."The level must be a number.");
                return;
            }
            $item = $player->getInventory()->getItemInHand();
            $item = Core::getCEManager()->addEnchantmentToPickaxe($item, $id, $level);
            $player->getInventory()->setItemInHand($item);
            $sender->sendMessage(Core::getRandomPrefix()."Command Success.");
        }
    }

}