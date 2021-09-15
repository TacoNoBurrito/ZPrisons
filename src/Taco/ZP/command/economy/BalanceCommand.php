<?php namespace Taco\ZP\command\economy;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class BalanceCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("bal", "§rCheck Your Balance.", "", ["balance", "mymoney"]);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $sender->sendMessage(Core::getRandomPrefix()."§7Current Balance §a$".Core::getSessionManager()->getPlayerSession($sender)->getMoney());
            return;
        }
        $this->sendNoConsole($sender);
    }

}