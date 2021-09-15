<?php namespace Taco\ZP\crates;

use pocketmine\item\Item;
use pocketmine\Player;
use function str_replace;

class CrateReward {

    private ?Item $item;

    private bool $command = false;

    private string $cmd = "";

    public function __construct(bool $command, Item $item = null, string $cmd = "") {
        if ($command) {
            $this->cmd = $cmd;
            $this->command = $command;
            return;
        }
        $this->item = $item;
    }

    public function getItem() : ?Item {
        return $this->item;
    }

    public function isCommand() : bool {
        return $this->command;
    }

    public function getCommand(Player $player) : string {
        return str_replace(["{player}"], [$player->getName()], $this->cmd);
    }

}