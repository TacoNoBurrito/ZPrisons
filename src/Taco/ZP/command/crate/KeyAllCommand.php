<?php namespace Taco\ZP\command\crate;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class KeyAllCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("keyall", "§rDo a KeyAll (staff)");
        $this->setPermission("staff.keyall");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender) or !$sender instanceof Player) {
            //TODO: key alls
            $sender->sendMessage(Core::getRandomPrefix()."If this isn't done when beta releases. annoy taco about it so he does it thx.");
            return;
        }
        $sender->sendMessage(EasyCommand::NO_PERMISSION);
    }

}