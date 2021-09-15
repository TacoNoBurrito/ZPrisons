<?php namespace Taco\ZP\random;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Taco\ZP\Core;
use function explode;
use function implode;
use function is_null;

class Forms {

    public const TEXTURE_CANCEL = "textures/ui/cancel.png";

    public function sendUpdatesForm(Player $player) : void {
        $form = new SimpleForm(function(Player $player, ?int $data) {});
        $form->setTitle("§l§dZ§bPrisons");
        $updates = [
            "§o§l§eWhats New?",
            " ",
            "§rCurrent Version: §e".Core::getInstance()->getServer()->getPluginManager()->getPlugin("ZPrisons")->getDescription()->getVersion(),
            " ",
            "§o§l§eUpdate Log!",
            "§r§f - Made the core.",
            " - No new updates."
        ];
        $form->setContent(implode("\n", $updates));
        $form->addButton("§cClose", 0, self::TEXTURE_CANCEL);
        $player->sendForm($form);
    }

    private function doWarp(Player $player, string $name, array $warpData) : void {
        if ($level = Core::getInstance()->getServer()->getLevelByName($warpData["level"])) {
            $pos = explode(":", $warpData["pos"]);
            $prev = $player->getLevel();
            $x = $player->getX();
            $z = $player->getZ();
            $player->teleport($level->getSpawnLocation());
            $player->teleport(new Vector3((float)$pos[0], (float)$pos[1], (float)$pos[2]));
            $pos = $player->getPosition();
            $prev->loadChunk($x >> 4, $z >> 4);
            $toText = $pos->getX() + 0.5 . ":" . ($pos->getY() + 1) . (":" . ($pos->getZ() + 0.5));

            Core::getEntityUtils()->spawnEntity("TeleportText", $toText, $level, "§b {$player->getName()} Warped To ".$name."...");
            return;
        }
        $player->sendMessage(Core::getRandomPrefix()."It looks like that warp is closed! Please try again later.");
    }

    public function openWarpsCategoryForm(Player $player, string $name, array $warpData) : void {
        $form = new SimpleForm(function(Player $player, ?int $data) use ($warpData) {
            if (is_null($data)) return;
            foreach ($warpData["warps"] as $name => $warpData) {
                if ($data < 1) {
                    $this->doWarp($player, $name, $warpData);
                    return;
                }
                $data--;
            }
        });
        $form->setTitle("§l§d".$name);
        $form->setContent("§r§bChoose a warp!");
        if ($name == "Mines") {
            $mine = Core::getRankupUtils()->letter2Num(Core::getSessionManager()->getPlayerSession($player)->getMine());
            foreach ($warpData["warps"] as $name => $data) {
                if (Core::getRankupUtils()->letter2Num($name) > $mine) {
                    unset($warpData["warps"][$name]);
                    continue;
                }
                $form->addButton("§b" . $name . "\n§dTap Me To Warp!");
            }
        } else {
            foreach ($warpData["warps"] as $name => $data) {
                $form->addButton("§b" . $name . "\n§dTap Me To Warp!");
            }
            $form->addButton("Close", 0, self::TEXTURE_CANCEL);
        }
        $player->sendForm($form);
    }

    public function openBaseWarpsForm(Player $player) : void {
        $warps = Core::getInstance()->config["warps"];
        $form = new SimpleForm(function(Player $player, ?int $data) use ($warps) {
            if (is_null($data)) return;
            foreach ($warps as $name => $warpData) {
                if ($data < 1) {
                    if (isset($warpData["warps"])) {
                        $this->openWarpsCategoryForm($player, $name, $warpData);
                        return;
                    }
                    $this->doWarp($player, $name, $warpData);
                    return;
                }
                $data--;
            }
        });
        $form->setTitle("§l§dW§bA§dR§bP§dS");
        $form->setContent("§r§bChoose a category, or a warp!");
        foreach ($warps as $name => $data) {
            if (isset($data["warps"])) {
                $form->addButton("§b".$name."\n§d".count($data)." Warp(s).");
            } else {
                $form->addButton("§b".$name."\n§dTap Me To Warp!");
            }
        }
        $form->addButton("Close", 0, self::TEXTURE_CANCEL);
        $player->sendForm($form);
    }

    public function openTagsForm(Player $player) : void {
        $session = Core::getSessionManager()->getPlayerSession($player);
        $tags = Core::getInstance()->config["tags"];
        $form = new SimpleForm(function(Player $player, ?int $data) use($tags, $session) {
            if (is_null($data)) return;
            foreach ($tags as $name => $permission) {
                if ($data < 1) {
                    if ($session->hasPermission($permission)) {
                        $player->sendMessage(Core::getRandomPrefix()."You do not have permission to use this tag! §eUnlock it by grinding, opening crates, trading, or winning them!");
                        return;
                    }
                    $session->setTag($name);
                    $player->sendMessage(Core::getRandomPrefix()."Successfully equipped the tag: ".$name."§r! §eThis tag can be taken off by running §e/tags clear§f.");
                    return;
                }
                $data--;
            }
        });
        $form->setTitle("§l§dT§bA§dG§bS");
        $form->setContent("§r§bChoose a tag! §oif its unlocked :)");
        foreach ($tags as $name => $permission) {
            $form->addButton("§r".$name."\n".($session->hasPermission($permission) ? "§aUNLOCKED!" : "§cNo Permissions!"));
        }
        $form->addButton("Close", 0, self::TEXTURE_CANCEL);
        $player->sendForm($form);
    }

}