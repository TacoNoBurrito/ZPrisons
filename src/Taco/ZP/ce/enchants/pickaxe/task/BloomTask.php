<?php namespace Taco\ZP\ce\enchants\pickaxe\task;

use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\sound\PopSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use Taco\ZP\Core;
use function strtolower;

class BloomTask extends Task {

    private Block $block;

    private Player $player;

    private int $times = 0;

    private int $moneyMade = 0;

    private AxisAlignedBB $aaBB;

    private float $multiplier = 0;

    private string $mine = "";

    public function __construct(Block $block, Player $player, AxisAlignedBB $aaBB, string $mine) {
        $this->block = $block;
        $this->player = $player;
        $this->aaBB = $aaBB;
        $this->mine = $mine;
        $this->multiplier = Core::getSessionManager()->getPlayerSession($player)->getMultiplier();
    }

    public function onRun(int $currentTick) : void {
        $this->times++;
        if ($this->times > 5) {
            $this->player->sendMessage("§l§5Bloom §r§7> §fBloom made §a$".$this->moneyMade."§f!");
            $this->getHandler()->cancel();
            return;
        }
        $block = $this->block;
        $radius = $this->times + 1;
        $minX = $block->x - $radius;
        $minZ = $block->z - $radius;
        $maxX = $block->x + $radius;
        $maxZ = $block->z + $radius;
        $level = $block->getLevel();
        $y = $block->getY();
        $money = 0;
        $prices = Core::getInstance()->config["sell-prices"];
        for ($x = $minX; $x <= $maxX; $x++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                $pos = $level->getBlockAt($x, $y + $this->times, $z);
                if ($this->aaBB->isVectorInside($pos)) {
                    if (isset($prices[$pos->getId().":".$pos->getDamage()])) {
                        $money += $prices[$pos->getId().":".$pos->getDamage()] * $this->multiplier;
                    }
                    Core::getInstance()->blocksInMine[$this->mine] -= 1;
                    if ((Core::getInstance()->blocksInMine[$this->mine] - 10) < 50) {
                        Core::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "mine reset ".$this->mine);
                        Core::getInstance()->blocksInMine[$this->mine] = Core::getInstance()->storedBlocksInMine[$this->mine];
                    }
                    $level->setBlock($pos, Block::get(0));
                }
            }
        }
        $this->moneyMade += $money;
        $this->player->getLevel()->addSound(new PopSound($this->player));
    }

}