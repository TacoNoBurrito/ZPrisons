<?php namespace Taco\ZP\command\progression;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use Taco\ZP\session\PlayerSession;

class RankupCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("rankup", "§rAscend Through The Mines!", null, ["ru"]);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $session = Core::getSessionManager()->getPlayerSession($sender);
            $rankup = $session->canRankup();
            if ($rankup == PlayerSession::RANKUP_CANT_PRESTIGE) {
                $sender->sendMessage(Core::getRandomPrefix()."You are at the mine Z! Do §e/prestige §fto get a higher §emultiplier §fand get access to more features!");
                return;
            }
            if ($rankup == PlayerSession::RANKUP_CANT_FUNDS) {
                $sender->sendMessage(Core::getRandomPrefix()."You do not have sufficient funds to rankup. You need §a$".$session->getRankupCost()."§f to rankup.");
                return;
            }
            $session->takeMoney($session->getRankupCost());
            $old = $session->getMine();
            $session->rankup();
            $sender->sendMessage(Core::getRandomPrefix()."You have successfully ranked up!");
            $sender->sendTitle("§l§dRankup!", "§r§o§b".$old." -> ".Core::getSessionManager()->getPlayerSession($sender)->getMine());
            return;
        }
        $this->sendNoConsole($sender);
    }

}