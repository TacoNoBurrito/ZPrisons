<?php namespace Taco\ZP\vp;

use pocketmine\Player;
use Taco\ZP\Core;
use Taco\ZP\vp\task\VotePartyTask;

class VotePartyManager {

    private int $votes;

    public function init() : void {
        $this->votes = Core::getInstance()->config["vote-party"];
    }

    public function getVotes() : int {
        return $this->votes;
    }

    public function addVote() : void {
        $this->votes++;
        if ($this->votes > 50) {
            $this->votes = 0;
            Core::getInstance()->getScheduler()->scheduleRepeatingTask(new VotePartyTask(), 20);
            Core::getInstance()->getServer()->broadcastMessage("§7 » §fA §aVoteParty§f is starting soon!");
        }
    }

    public function doVoteForPlayer(Player $player) : void {
        $player->sendMessage(Core::getRandomPrefix()."Thanks for voting for §l§dZ§bPrisons §fyou have recieved your rewards.");
        Core::getInstance()->getServer()->broadcastMessage("§7 » §e".$player->getName()." §fhas voted for §l§dZ§bPrisons §fand has recieved §e5x Vote Crate Keys §7| §e1x God Pickaxe Shard");
        //TODO: give stuff lol
        $this->addVote();
    }

}