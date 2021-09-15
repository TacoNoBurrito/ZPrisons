<?php namespace Taco\ZP\command\random;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class WarpCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("warp", "§rWarp To New Areas!", "", []);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            Core::getForms()->openBaseWarpsForm($sender);
            return;
        }
        $this->sendNoConsole($sender);
    }

}