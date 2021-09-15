<?php namespace Taco\ZP\command\random;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;

class TagCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("tag", "§rOpen The Tags Menu!", "", ["tags"]);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $arg = array_shift($args);
            if ($arg == "") {
                Core::getForms()->openTagsForm($sender);
                return;
            }
            if ($arg == "clear") {
                Core::getSessionManager()->getPlayerSession($sender)->setTag("");
                $sender->sendMessage(Core::getRandomPrefix()."Your tag has been cleared.");
            }
            return;
        }
        $this->sendNoConsole($sender);
    }

}