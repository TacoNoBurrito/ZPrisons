<?php namespace Taco\ZP\vp\task;

use pocketmine\scheduler\Task;
use Taco\ZP\Core;

class VotePartyTask extends Task {

    private int $timeUntilStart = 10;

    private bool $started = false;

    private int $ticked = 0;

    public function onRun(int $currentTick) : void {
        if ($this->started) {
            //TODO: DO REWARDS
            $this->ticked++;
            if ($this->ticked > 15) {
                $this->getHandler()->cancel();
                Core::getInstance()->getServer()->broadcastMessage("§7 » §eThe VoteParty Has Ended!");
                return;
            }
            return;
        }
        if (!$this->timeUntilStart < 1) {
            Core::getInstance()->getServer()->broadcastMessage("§6* §a§lVoteParty §r§fStarting In §e".$this->timeUntilStart." Seconds!");
            $this->timeUntilStart++;
        } else {
            $this->started = true;
            foreach (Core::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $player->sendPopup("§l§aVOTE PARTY", "§r§fOpen Some Inventory Spots!");
            }
        }
    }

}