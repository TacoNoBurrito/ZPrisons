<?php namespace Taco\ZP\command\staff;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;

class AddWarpCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("addwarp", "§rAdd a Warp (staff)", "", []);
        $this->setPermission("staff.addwarp");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($sender instanceof Player) {
            if ($this->testPermission($sender)) {
                $category = array_shift($args);
                if ($category == "") {
                    $sender->sendMessage(Core::getRandomPrefix()."Please provide whether this warp will be in a category. <no|yes>");
                    return;
                }
                $name = array_shift($args);
                if ($name == "") {
                    $sender->sendMessage(Core::getRandomPrefix()."Please provide a name for the warp.");
                    return;
                }
                if ($category == "yes") {
                    $category = array_shift($args);
                    if ($category == "") {
                        $sender->sendMessage(Core::getRandomPrefix()."Please provide a category for the warp.");
                        return;
                    }
                    Core::getInstance()->config["warps"][$category]["warps"][$name] = ["pos" => $sender->getX().":".$sender->getY().":".$sender->getZ(), "level" => $sender->getLevel()->getName()];
                    $sender->sendMessage(Core::getRandomPrefix()."Successfully added warp to category: ".$category.".");
                    return;
                }
                Core::getInstance()->config["warps"][$name] = ["pos" => $sender->getX().":".$sender->getY().":".$sender->getZ(), "level" => $sender->getLevel()->getName()];
                $sender->sendMessage(Core::getRandomPrefix()."Successfully added warp.");
                return;
            }
            $sender->sendMessage(self::NO_PERMISSION);
            return;
        }
        $this->sendNoConsole($sender);
    }

}