<?php namespace Taco\ZP\kit;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use Taco\ZP\kit\types\Pickaxe;
use Taco\ZP\Core;
use Taco\ZP\random\Forms;
use Taco\ZP\random\TimeUtils;
use function count;
use function is_null;

class KitManager {

    private array $kits = [];

    public function init() : void {
        $this->kits = [
            new Pickaxe()
        ];
    }

    public function getKitCooldown(Player $player, KitType $kit) : int {
        return Core::getSessionManager()->getPlayerSession($player)->getKitCooldown($kit);
    }

    public function putOnCooldown(Player $player, string $kit) : void {
        Core::getSessionManager()->getPlayerSession($player)->putOnKitCooldown($kit);
    }

    public function openKitListForm(Player $player) : void {
        $session = Core::getSessionManager()->getPlayerSession($player);
        $form = new SimpleForm(function(Player $player, ?int $data) {
            if (is_null($data)) return;
            if ($data > count($this->kits) - 1) return;
            $kit = $this->kits[$data];
            $session = Core::getSessionManager()->getPlayerSession($player);
            if ($kit instanceof KitType) {
                if (!$session->hasPermission($kit->getPermission()) and !$kit->getPermission() == "none") {
                    $player->sendMessage(Core::getRandomPrefix()."§fYou do not have permission to use this kit!");
                    return;
                }
                $cooldown = $this->getKitCooldown($player, $kit);
                if ($cooldown > 0) {
                    $player->sendMessage(Core::getRandomPrefix()."§fYou are still on cooldown for this kit! §7(§e".TimeUtils::intToTimeString($cooldown - $this->getKitCooldown($player, $kit))."§7)");
                    return;
                }
                $dropped = false;
                foreach ($kit->getItems() as $item) {
                    if ($player->getInventory()->canAddItem($item)) $player->getInventory()->addItem($item);
                    else {
                        $player->getLevel()->dropItem($player->getPosition(), $item);
                        $dropped = true;
                    }
                }
                $player->sendMessage(Core::getRandomPrefix()."§fYou have equipped the kit §e".$kit->getName()."!".($dropped ? " §fYour inventory space was full. So some items have dropped on the ground!" : ""));
                $this->putOnCooldown($player, $kit->getName());
            }
        });
        $form->setTitle("§l§bK§dI§bT§dS");
        $form->setContent("§bChoose a kit!");
        foreach ($this->kits as $class) {
            if ($class instanceof KitType) {
                if (!$session->hasPermission($class->getPermission()) and $class->getPermission() !== "none") {
                    $form->addButton($class->getName()."\n".("§cNo Permission!"));
                    continue;
                }
                if ($this->getKitCooldown($player, $class) < 1) {
                    $time = "Ready To Equip";
                } else {
                    $time = TimeUtils::intToTimeString($this->getKitCooldown($player, $class));
                }
                $form->addButton($class->getName()."\n".($time));
            }
        }
        $form->addButton("Close", 0, Forms::TEXTURE_CANCEL);
        $player->sendForm($form);
    }

}