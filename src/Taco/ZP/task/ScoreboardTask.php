<?php namespace Taco\ZP\task;

use pocketmine\scheduler\Task;
use Taco\ZP\Core;

class ScoreboardTask extends Task {

    public function onRun(int $currentTick) : void {
        $sessionManager = Core::getSessionManager();
        foreach (Core::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $sessionManager->getPlayerSession($player)->updateScoreboard();
        }
    }

}