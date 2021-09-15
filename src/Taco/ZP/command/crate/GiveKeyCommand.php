<?php namespace Taco\ZP\command\crate;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;
use function explode;

class GiveKeyCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("givekey", "§rGive a Key To A Player (staff)");
        $this->setPermission("staff.givekey");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender) or !$sender instanceof Player) {
            $player = array_shift($args);
            if ($player == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a player to give a key to.");
                return;
            }
            $player = Core::getInstance()->getServer()->getPlayer($player);
            if ($player == null) {
                $sender->sendMessage(Core::getRandomPrefix()."This player is not online or doesn't exist.");
                return;
            }
            $type = array_shift($args);
            if ($type == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a key type.");
                return;
            }
            $keys = Core::getInstance()->config["crates"];
            $valid = false;
            foreach ($keys as $key) {
                $kType = explode(":", $key)[4];
                if ($kType == $type) {
                    $valid = true;
                    break;
                }
            }
            if ($valid) {
                Core::getCratesManager()->giveKey($player, $type);
                $sender->sendMessage(Core::getRandomPrefix()."Successfully gave §a".$player->getName()." §fa §e".$type." §fkey.");
                return;
            }
            $sender->sendMessage(Core::getRandomPrefix()."Please provide a valid key type.");
            return;
        }
        $sender->sendMessage(EasyCommand::NO_PERMISSION);
    }

}