<?php namespace Taco\ZP\text;

use pocketmine\entity\Entity;
use pocketmine\level\Position;
use function strtoupper;

class CFTManager {

    /**
     * @var array<string, CustomFloatingText>
     */
    public array $mineText = [];

    public function addMineFloatingText(string $mine, Position $pos) : void {
        $this->mineText[$mine] = new CustomFloatingText("§b".strtoupper($mine)." §7Mine\n§7Amount Left: §b%100.00", $pos, Entity::$entityCount++);
    }

}