<?php namespace Taco\ZP;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\Pickaxe;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use Taco\ZP\ce\types\PickaxeEnchantment;
use Taco\ZP\ce\types\ArmorEnchantment;

class EventListener implements Listener {

    private array $chatCD = [];

    private array $interactCD = [];

    private bool $cancel_send = true;

    public function onLogin(PlayerLoginEvent $event) : void {
        $player = $event->getPlayer();
        Core::getSessionManager()->createSession($player);
        $this->chatCD[$player->getName()] = 0;
        $this->interactCD[$player->getName()] = 0;
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        $sessionManager = Core::getSessionManager();
        unset($this->chatCD[$player->getName()]);
        unset($this->interactCD[$player->getName()]);
        if ($sessionManager->getPlayerSession($player)->isFrozen()) {
            $player->setBanned(true);
            $event->setQuitMessage("".$sessionManager->getPlayerSession($player)->getFormattedRank()."§r§f ".$player->getName()." §r§flogged out whilst frozen. §6Is now banned.");
            $sessionManager->closeSession($player);
            return;
        }
        $event->setQuitMessage(" §c(-) §r".$sessionManager->getPlayerSession($player)->getFormattedRank()."§r§f ".$player->getName());
        $sessionManager->closeSession($player);
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $event->setJoinMessage(" §a(+)  §r".Core::getSessionManager()->getPlayerSession($player)->getFormattedRank()." §r§f".$player->getName());
        Core::getForms()->sendUpdatesForm($player);
        $player->setImmobile(false);
    }

    public function onBreak(BlockBreakEvent $event) : void {
        $player = $event->getPlayer();
        $session = Core::getSessionManager()->getPlayerSession($player);
        if ($session->isFrozen()) {
            $player->sendMessage(Core::getRandomPrefix()."You cannot do this whilst frozen.");
            $event->setCancelled(true);
            return;
        }
        $block = $event->getBlock();
        foreach(Core::getInstance()->mineReset->getMineManager()->getMines() as $mine) {
            if($mine->isPointInside($block)) {
                Core::getInstance()->blocksInMine[$mine->getName()] -= 1;
                if ((Core::getInstance()->blocksInMine[$mine->getName()] - 10) < 50) {
                    Core::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "mine reset ".$mine->getName());
                    Core::getInstance()->blocksInMine[$mine->getName()] = Core::getInstance()->storedBlocksInMine[$mine->getName()];
                }
                $session->addBlockMined();
                $session->setLine(4, " §a» §7Blocks §f".Core::getNumberUtils()->intToPrefix($session->getBlocksMined()), $player);
                $event->setXpDropAmount(0);
                if ($session->isPickupBlocks()) return;
                $event->setDrops([]);
                $id = $block->getId().":".$block->getDamage();
                if (isset(Core::getInstance()->config["sell-prices"][$id])) {
                    $item = $player->getInventory()->getItemInHand();
                    PickaxeEnchantment::toggle($event, $player->getInventory()->getItemInHand(), $mine);
                    $sell = Core::getInstance()->config["sell-prices"][$id];
                    $sell = $sell * $session->getMultiplier();
                    $sell = $sell * Core::getGemManager()->getExtraMultiplier($item);
                    $session->addMoney($sell);
                    $session->setLine(6, " §b» §7Balance §f$".Core::getNumberUtils()->intToPrefix($session->getMoney()), $player);
                    if ($item->getNamedTag()->hasTag("blocks")) {
                        $blocks = $item->getNamedTag()->getInt("blocks");
                        $item->getNamedTag()->setInt("blocks", $blocks+1);
                        $player->getInventory()->setItemInHand($item);
                        if (($blocks % 10) == 0) {
                            $player->getInventory()->setItemInHand(Core::getPickaxeUtils()->updateLore($item));
                        }
                    }
                }
                return;
            }
        }
        if ($player->isOp()) return;
        if ($session->isBuilderMode()) return;
        $event->setCancelled(true);
    }

    public function onPlace(BlockPlaceEvent $event) : void {
        $player = $event->getPlayer();
        $session = Core::getSessionManager()->getPlayerSession($player);
        if ($session->isFrozen()) {
            $player->sendMessage(Core::getRandomPrefix()."You cannot do this whilst frozen.");
            $event->setCancelled(true);
            return;
        }
        if ($player->isOp()) return;
        if ($session->isBuilderMode()) return;
        $event->setCancelled(true);
    }

    public function onChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        if (time() - $this->chatCD[$player->getName()] < 1 and !$player->isOp()) {
            $player->sendMessage(Core::getRandomPrefix()."§cPlease don't spam in chat.");
            $event->setCancelled(true);
            return;
        }
        $this->chatCD[$player->getName()] = time();
        $msg = $event->getMessage();
        $event->setFormat(Core::getSessionManager()->getPlayerSession($player)->getMessageFormat($msg));
    }

    public function onArmor(EntityArmorChangeEvent $event) : void {
        ArmorEnchantment::toggle($event);
    }

    public function onDataPacketSend(DataPacketSendEvent $event) : void {
        if($this->cancel_send and $event->getPacket() instanceof ContainerClosePacket) {
            $event->setCancelled(true);
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
        if($event->getPacket() instanceof ContainerClosePacket) {
            $this->cancel_send = false;
            $event->getPlayer()->sendDataPacket($event->getPacket(), false, true);
            $this->cancel_send = true;
        }
    }

    public function onHunger(PlayerExhaustEvent $event) : void {
        $event->setCancelled(true);
    }

    public function onTeleport(EntityTeleportEvent $event) : void {
        $entity = $event->getEntity();
        $to = $event->getTo()->getLevel();
        if ($entity instanceof Player) {
            foreach (Core::getCFTManager()->mineText as $obj) {
                if ($obj->getPosition()->getLevel()->getName() === $event->getFrom()->getLevel()->getName()) {
                    $obj->remove($entity);
                } else {
                    if ($obj->getPosition()->getLevel()->getName() == $to->getName()) {
                        $obj->spawn($entity);
                    }
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        if (time() - $this->interactCD[$player->getName()] < 1) {
            return;
        }
        $this->interactCD[$player->getName()] = time();
        $block = $event->getBlock();
        $crate = Core::getCratesManager()->getCrateAtPosition($block);
        $item = $player->getInventory()->getItemInHand();
        if ($crate !== null) {
            if (!$item->getNamedTag()->hasTag("crateType")) {
                $player->sendMessage(Core::getRandomPrefix()."That is not a valid crate key!");
                return;
            }
            if ($item->getNamedTag()->getString("crateType") !== $crate) {
                $player->sendMessage(Core::getRandomPrefix()."That key does not go with this crate!");
                return;
            }
            $player->getInventory()->setItemInHand($item->setCount($item->getCount()-1));
            Core::getCratesManager()->openCrate($player, $crate);
        }
    }

    public function onEntityDamage(EntityDamageEvent $event) : void {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $cause = $event->getCause();
            switch($cause) {
                case EntityDamageEvent::CAUSE_VOID:
                    $player->sendMessage(Core::getRandomPrefix()."You have been saved from the void!");
                    $event->setCancelled(true);
                    $player->teleport(Core::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
                    break;
                case EntityDamageEvent::CAUSE_FALL:
                case EntityDamageEvent::CAUSE_SUFFOCATION:
                    $event->setCancelled(true);
                    break;
                case 5:
                case 6:
                    if ($player->getLevel()->getName() !== "pvpmine") {
                        $player->extinguish();
                        $event->setCancelled(true);
                    }
                    break;
            }
        }
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void {
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if ($player instanceof Player and $damager instanceof Player) {
            $damager->sendMessage(Core::getRandomPrefix()."PvP is currently disabled during BETA.");
            $event->setCancelled(true);
        }
    }

    public function onMove(PlayerMoveEvent $event) : void {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        $to = $event->getTo();
        if ($from->distance($to) < 0.1) {
            return;
        }
        $maxDistance = 16;
        foreach ($player->getLevel()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $e) {
            if($e instanceof Player) {
                continue;
            }
            if(substr($e->getSaveId(), 0, 7) !== "Slapper") {
                continue;
            }
            switch ($e->getSaveId()) {
                case "SlapperFallingSand":
                case "SlapperMinecart":
                case "SlapperBoat":
                case "SlapperPrimedTNT":
                case "SlapperShulker":
                    continue 2;
            }
            $xdiff = $player->x - $e->x;
            $zdiff = $player->z - $e->z;
            $angle = atan2($zdiff, $xdiff);
            $yaw = (($angle * 180) / M_PI) - 90;
            $ydiff = $player->y - $e->y;
            $v = new Vector2($e->x, $e->z);
            $dist = $v->distance($player->x, $player->z);
            $angle = atan2($dist, $ydiff);
            $pitch = (($angle * 180) / M_PI) - 90;
            if ($e->getSaveId() === "SlapperHuman") {
                $pk = new MovePlayerPacket();
                $pk->entityRuntimeId = $e->getId();
                $pk->position = $e->asVector3()->add(0, $e->getEyeHeight(), 0);
                $pk->yaw = $yaw;
                $pk->pitch = $pitch;
                $pk->headYaw = $yaw;
                $pk->onGround = $e->onGround;
            } else {
                $pk = new MoveActorAbsolutePacket();
                $pk->entityRuntimeId = $e->getId();
                $pk->position = $e->asVector3();
                $pk->xRot = $pitch;
                $pk->yRot = $yaw;
                $pk->zRot = $yaw;
            }
            $player->dataPacket($pk);
        }
    }

    public function onCraft(CraftItemEvent $event) : void {
        $player = $event->getPlayer();
        $output = $event->getOutputs();
        foreach ($output as $out) {
            if ($out instanceof Pickaxe) {
                $event->setCancelled(true);
                $player->sendMessage(Core::getRandomPrefix()."You cannot craft a pickaxe.");
            }
        }
    }

}