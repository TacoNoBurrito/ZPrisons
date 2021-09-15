<?php namespace Taco\ZP\command\progression;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use Taco\ZP\session\PlayerSession;

class PrestigeCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("prestige", "§rGet Rewards and Better Mines!", null, []);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $session = Core::getSessionManager()->getPlayerSession($sender);
            $canPrestige = $session->canPrestige();
            if ($canPrestige == PlayerSession::PRESTIGE_CANT_NOT_Z) {
                $sender->sendMessage(Core::getRandomPrefix()."You cannot prestige until you are Z Mine!");
                return;
            }
            if ($canPrestige == PlayerSession::PRESTIGE_CANT_FUNDS) {
                $sender->sendMessage(Core::getRandomPrefix()."You do not have sufficient funds to prestige. You need §a$".$session->getPrestigeCost()."§f to prestige.");
                return;
            }
            $sender->sendTitle("§l§bPRESTIGE!", "§r§o§d" . $session->getPrestige() . (" -> " . ($session->getPrestige() + 1)));
            $session->takeMoney($session->getPrestigeCost());
            $session->prestige();
            $session->setMine("A");
            $session->addMultiplier(1.2);
            $sender->sendMessage(Core::getRandomPrefix()."You have successfully ascended to the next prestige!");
            $sender->teleport(Core::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
            return;
        }
        $this->sendNoConsole($sender);
    }

}