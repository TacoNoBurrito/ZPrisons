<?php namespace Taco\ZP\command\staff;

use pocketmine\command\CommandSender;
use Taco\ZP\command\EasyCommand;
use Taco\ZP\Core;
use function array_shift;

class AddTagCommand extends EasyCommand {

    public function __construct() {
        parent::__construct("addtag", "§rAdd a Tag To The Tags List (staff)", "", []);
        $this->setPermission("staff.addtag");
    }

    public function exec(CommandSender $sender, array $args) : void {
        if ($this->testPermission($sender)) {
            $tag = array_shift($args);
            if ($tag == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a tag to add.");
                return;
            }
            $permission = array_shift($args);
            if ($permission == "") {
                $sender->sendMessage(Core::getRandomPrefix()."Please provide a permission for the tag.");
                return;
            }
            Core::getInstance()->config["tags"][$tag] = $permission;
            $sender->sendMessage(Core::getRandomPrefix()."Successfully added a tag with the name: ".$tag." §r");
            return;
        }
        $sender->sendMessage(EasyCommand::NO_PERMISSION);
    }

}