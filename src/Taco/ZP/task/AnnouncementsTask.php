<?php namespace Taco\ZP\task;

use pocketmine\scheduler\Task;
use Taco\ZP\Core;
use function array_rand;

class AnnouncementsTask extends Task {

    public function onRun(int $currentTick) : void {
        $announcements = Core::getInstance()->config["announcements"];
        Core::getInstance()->getServer()->broadcastMessage("§a   §b\n§7 » ".$announcements[array_rand($announcements)]."\n§e      §d");
    }

}