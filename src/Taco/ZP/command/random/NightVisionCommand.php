<?php namespace Taco\ZP\command\random;

use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Pickaxe;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;

class NightVisionCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("nv", "§rGive Yourself Night Vision For 10 Minutes.", "", ["nightvision"]);
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 20 * 600, 1));
            $sender->sendMessage(Core::getRandomPrefix()."You have been given night vision.");
            return;
        }
        $this->sendNoConsole($sender);
    }

}