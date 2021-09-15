<?php namespace Taco\ZP\task;

use pocketmine\scheduler\Task;
use Taco\ZP\Core;
use function strtoupper;

class UpdateMineText extends Task {

    public function onRun(int $currentTick) : void {
        $blocks = Core::getInstance()->blocksInMine;
        $stored = Core::getInstance()->storedBlocksInMine;
        foreach (Core::getCFTManager()->mineText as $mine => $obj) {
            $level = $obj->getPosition()->getLevel();
            foreach ($level->getPlayers() as $player) {
                $pct = (string)((float)($blocks[$mine] / $stored[$mine]) * 100);
                $obj->update("§b".strtoupper($mine)." §7Mine\n§7Amount Left: §b".$pct, $player);
            }
        }
    }

}