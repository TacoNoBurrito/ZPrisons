<?php namespace Taco\ZP\session;

use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use Taco\ZP\Core;
use Taco\ZP\group\GroupPermissionsList;
use Taco\ZP\kit\KitType;
use function in_array;
use function pow;

class PlayerSession {

    public const RANKUP_CAN = 0;

    public const RANKUP_CANT_FUNDS = 1;

    public const RANKUP_CANT_PRESTIGE = 2;

    public const PRESTIGE_CANT_FUNDS = 3;

    public const PRESTIGE_CAN = 4;

    public const PRESTIGE_CANT_NOT_Z = 5;

    private array $data = [
        "kills" => 0,
        "deaths" => 0,
        "killstreak" => 0,
        "money" => 0,
        "gang" => "",
        "multiplier" => "1.0",
        "prestige" => 0,
        "rank" => "Guest",
        "permissions" => [],
        "mine" => "A",
        "tag" => "",
        "chatcolor" => "§7",
        "mined" => 0,
        "kits" => []
    ];

    private bool $pickupBlocks = false;

    private Player $player;

    private bool $builderMode = false;

    private bool $frozen = false;

    private array $line = [];

    public function __construct(Player $player) {
        $this->player = $player;
        $data = Core::getInstance()->database;
        if ($data = $data->get($player->getName())) $this->data = $data;
        else {
            $real = [];
            $helmet = Item::get(ItemIds::DIAMOND_HELMET);
            $chestplate = Item::get(ItemIds::DIAMOND_CHESTPLATE);
            $leggings = Item::get(ItemIds::DIAMOND_LEGGINGS);
            $boots = Item::get(ItemIds::DIAMOND_BOOTS);
            /*** @var Item $item */
            foreach ([$helmet, $chestplate, $leggings, $boots] as $item) {
                $item->setCustomName("§r§fStarter ".$item->getName());
                $item->setLore(["\n\n§r§7Your journey starts here.\n§r§7§oGoodluck, prisoner."]);
                $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10));
                $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10));
                $real[] = $item;
            }
            $sword = Item::get(ItemIds::DIAMOND_SWORD);
            $sword->setCustomName("§r§fStarter Sword");
            $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 7));
            $sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 4));
            $pickaxe = Item::get(ItemIds::DIAMOND_PICKAXE);
            $pickaxe = Core::getPickaxeUtils()->initPickaxe($pickaxe);
            $axe = Item::get(ItemIds::DIAMOND_AXE);
            $shovel = Item::get(ItemIds::DIAMOND_SHOVEL);
            /*** @var Item $tool */
            foreach ([$pickaxe, $axe, $shovel] as $tool) {
                $tool->setCustomName("§r§fStarter ".$tool->getName());
                $tool->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 15));
                if ($tool instanceof Durable) $tool->setUnbreakable(true);
                $real[] = $tool;
            }
            $gaps = Item::get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 5);
            $real[] = $gaps;
            $real[] = $sword;
            foreach ($real as $iteme) {
                $player->getInventory()->addItem($iteme);
            }
        }
    }

    public function save() : void {
        $name = $this->player->getName();
        Core::getInstance()->database->remove($name);
        Core::getInstance()->database->set($name, $this->data);
        Core::getInstance()->database->save();
    }

    public function hasPermission(string $permission) : bool {
        if ($this->player->isOp()) return true;
        if (isset(GroupPermissionsList::GROUP_PERMS[$this->getRank()])) {
            if (in_array($permission, GroupPermissionsList::GROUP_PERMS[$this->getRank()])) return true;
        }
        return in_array($permission, $this->data["permissions"]);
    }

    public function addPermission(string $permission) : void {
        $this->data["permissions"][] = $permission;
    }

    public function removePermission(string $permission) : void {
        unset($this->data["permissions"][$permission]);
    }

    public function addBlockMined() : void {
        $this->data["mined"]++;
    }

    public function getBlocksMined() : int {
        return $this->data["mined"];
    }

    public function getRank() : string {
        return $this->data["rank"];
    }

    public function getFormattedRank() : string {
        return [
            "Guest" => "§f§7Guest",
            "Voter" => "§l§aVoter",
            "Coal" => "§l§8Coal",
            "Iron" => "§l§fIron",
            "Gold" => "§l§6Gold",
            "Diamond" => "§l§bDiamond",
            "Helper" => "§l§eHelper",
            "Mod" => "§l§5Mod",
            "Admin" => "§l§cAdmin",
            "Owner" => "§l§2Owner",
            "Developer" => "§l§6Developer"
        ][$this->data["rank"]];
    }

    public function rankup() : void {
        $this->data["mine"] = Core::getRankupUtils()->num2Letter(Core::getRankupUtils()->letter2Num($this->data["mine"]) + 1);
    }

    public function setMine(string $mine) : void {
        $this->data["mine"] = $mine;
    }

    public function prestige() : void {
        $this->data["mine"] = "A";
        $this->data["prestige"] += 1;
        $this->data["multiplier"] = (string)((float)$this->data["multiplier"] + 0.5);
    }

    public function getMoney() : int {
        return $this->data["money"];
    }

    public function addMoney(int $amount) : void {
        $this->data["money"] += $amount;
    }

    public function takeMoney(int $amount) : void {
        $this->addMoney(-$amount);
    }

    public function getMine() : string {
        return $this->data["mine"];
    }

    public function getPrestige() : int {
        return $this->data["prestige"];
    }

    public function canRankup() : int {
        if ($this->getMine() == "Z") return self::RANKUP_CANT_PRESTIGE;
        if ($this->getMoney() >= $this->getRankupCost()) return self::RANKUP_CAN;
        return self::RANKUP_CANT_FUNDS;
    }

    public function setRank(string $rank) : void {
        $this->data["rank"] = $rank;
    }

    // Automatic Rankup Cost Generation
    public function getRankupCost() : int {
        $n = Core::getRankupUtils()->letter2Num($this->getMine()) + 1;
        $prestige = $this->getPrestige() == 0 ? 1 : $this->getPrestige();
        return abs((($n + 7) * 378 + pow(floor($n * 2 / 2), 1)) * $prestige);
    }

    // Automatic Prestige Cost Generation
    public function getPrestigeCost() : int {
        return $this->getPrestige() * 1000000 + 12013948;
    }

    public function canPrestige() : int {
        if (!$this->getMine() == "Z") return self::PRESTIGE_CANT_NOT_Z;
        if ($this->getMoney() >= $this->getPrestigeCost()) return self::PRESTIGE_CAN;
        return self::PRESTIGE_CANT_FUNDS;
    }

    public function isPickupBlocks() : bool {
        return $this->pickupBlocks;
    }

    public function setPickupBlocksState(bool $pickupBlocks) : void {
        $this->pickupBlocks = $pickupBlocks;
    }

    public function isBuilderMode() : bool {
        return $this->builderMode;
    }

    public function setBuilderModeState(bool $builderMode) : void {
        $this->builderMode = $builderMode;
    }

    public function isFrozen() : bool {
        return $this->frozen;
    }

    public function setFrozenState(bool $frozen) : void {
        $this->frozen = $frozen;
    }

    public function setTag(string $tag) : void {
        $this->data["tag"] = $tag;
    }

    public function getTag() : string {
        return $this->data["tag"];
    }


    public function getMessageFormat(string $msg) : string {
        $rank = $this->getFormattedRank();
        $tag = $this->getTag();
        $mine = $this->getMine();
        $prestige = $this->getPrestige();
        return "§f[§b".$mine."§e-§b".$prestige."§f] §r§f[".$rank."§f] ".($tag == "" ? "" : $tag." §r§f").$this->player->getName()." §r§f» ".$this->data["chatcolor"].$msg;
    }

    public function updateScoreboard() : void {
        $player = $this->player;
        $this->showScoreboard($player);//»
        $this->clearLines($player);//§$
        $next = Core::getRankupUtils()->num2Letter(Core::getRankupUtils()->letter2Num($this->getMine()) + 1);
        if ($this->getMine() == "Z")  {
            $next = "Prestige";
        }
        $this->addLine("§l§aPLAYER", $player);//tag - player - green
        $this->addLine(" §a» §7Rank §f".$this->getMine(), $player);//rankprison
        $this->addLine(" §a» §7Next Mine §f".$next, $player);//pct to next mine
        $this->addLine(" §a» §7Blocks §f".Core::getNumberUtils()->intToPrefix($this->getBlocksMined()), $player);//blocks mined
        $this->addLine("§l§bBALANCE", $player);//tag - bal - aqua
        $this->addLine(" §b» §7Balance §f$".Core::getNumberUtils()->intToPrefix($this->getMoney()), $player);//bal
        $this->addLine(" §b» §7Multiplier §fx".$this->data["multiplier"], $player);//multi
        $this->addLine("§l§dSTATS", $player);//tag - stats - pink
        $this->addLine(" §d» §7Kills §f".$this->data["kills"], $player);//k
        $this->addLine(" §d» §7Deaths §f".$this->data["deaths"], $player);//d
        $this->addLine(" §d» §7KDR §f".($this->data["kills"] == 0 or $this->data["deaths"] == 0 ? "0" : $this->data["kills"] / $this->data["deaths"]), $player);//kdr
        $this->addLine("§l§cSERVER", $player);//server - red
        $this->addLine(" §c» §7VoteParty §f".Core::getVotePartyManager()->getVotes()."§7/§f50", $player);//voteparty
        $this->addLine(" §c» §7IP §f§oPlay.ZPrisons.XYZ", $player);//ip
    }

    public function getMultiplier() : float {
        return (float)$this->data["multiplier"];
    }

    public function addMultiplier(float $multi) : void {
        $new = (float)$this->data["multiplier"] + $multi;
        $this->data["multiplier"] = (string)$new;
    }

    public function showScoreboard(Player $player) : void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = "sidebar";
        $pk->objectiveName = $player->getName();
        $pk->displayName = "§l§dZ§bPrisons §7[§6".count(Core::getInstance()->getServer()->getOnlinePlayers())."§7]";
        $pk->criteriaName = "dummy";
        $pk->sortOrder = 0;
        $player->sendDataPacket($pk);
    }

    public function addLine(string $line, Player $player) : void {
        $score = count($this->line) + 1;
        $this->setLine($score,$line,$player);
    }

    public function removeScoreboard(Player $player) : void {
        $objectiveName = $player->getName();
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $objectiveName;
        $player->sendDataPacket($pk);
    }

    public function clearLines(Player $player) {
        for ($line = 0; $line <= 15; $line++) {
            $this->removeLine($line, $player);
        }
    }

    public function setLine(int $loc, string $msg, Player $player) : void {
        $pk = new ScorePacketEntry();
        $pk->objectiveName = $player->getName();
        $pk->type = $pk::TYPE_FAKE_PLAYER;
        $pk->customName = $msg;
        $pk->score = $loc;
        $pk->scoreboardId = $loc;
        if (isset($this->line[$loc])) {
            unset($this->line[$loc]);
            $pkt = new SetScorePacket();
            $pkt->type = $pkt::TYPE_REMOVE;
            $pkt->entries[] = $pk;
            $player->sendDataPacket($pkt);
        }
        $pkt = new SetScorePacket();
        $pkt->type = $pkt::TYPE_CHANGE;
        $pkt->entries[] = $pk;
        $player->sendDataPacket($pkt);
        $this->line[$loc] = $msg;
    }

    public function removeLine(int $line, Player $player) : void {
        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_REMOVE;
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $player->getName();
        $entry->score = $line;
        $entry->scoreboardId = $line;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
        if (isset($this->line[$line])) {
            unset($this->line[$line]);
        }
    }

    public function getKitCooldown(KitType $class) : int {
        if (!isset($this->data["kits"][$class->getName()])) {
            return 0;
        }
        return ($this->data["kits"][$class->getName()] + $class->getKitCooldown()) - time();
    }

    public function putOnKitCooldown(string $class) : void {
        $this->data["kits"][$class] = time();
    }

}