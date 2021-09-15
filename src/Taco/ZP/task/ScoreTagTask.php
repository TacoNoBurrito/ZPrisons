<?php namespace Taco\ZP\task;

use pocketmine\scheduler\Task;
use Taco\ZP\Core;

class ScoreTagTask extends Task {

    public function onRun(int $currentTick) : void {
        $sessionMgr = Core::getSessionManager();
        foreach (Core::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $player->setScoreTag("§6Blocks Mined§8: §f".$sessionMgr->getPlayerSession($player)->getBlocksMined());
        }
    }

}