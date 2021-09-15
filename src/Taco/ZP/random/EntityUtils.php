<?php namespace Taco\ZP\random;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use Taco\ZP\Core;
use function explode;
use function strlen;
use function strtoupper;

class EntityUtils {



    public function spawnLightning(Position $pos) : void {
        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->entityRuntimeId = Entity::$entityCount++;
        $light->metadata = [];
        $light->motion = null;
        $light->position = new Vector3($pos->getX(), $pos->getY(), $pos->getZ());
        $light->pitch = 10;
        $light->yaw = 10;
        Core::getInstance()->getServer()->broadcastPacket($pos->getLevel()->getPlayers(), $light);
    }


}