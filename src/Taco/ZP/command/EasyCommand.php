<?php namespace Taco\ZP\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Taco\ZP\Core;

abstract class EasyCommand extends Command {

    public const NO_PERMISSION = "§cYou do not have permission to use this command.";

    public function __construct(string $name, string $description = "", string $usageMessage = null, $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        $this->exec($sender, $args);
    }

    abstract function exec(CommandSender $sender, array $args) : void;

    public function testPermission(CommandSender $target) : bool {
        if ($target instanceof Player) {
            if ($target->isOp()) return true;
            return Core::getSessionManager()->getPlayerSession($target)->hasPermission($this->getPermission());
        }
        return true;
    }

    public function sendNoConsole(CommandSender $sender) : void {
        $sender->sendMessage(Core::getRandomPrefix()."This command cannot be used in the console.");
    }

}