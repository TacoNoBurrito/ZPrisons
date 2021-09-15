<?php namespace Taco\ZP\command;

use Taco\ZP\ce\CECommand;
use Taco\ZP\command\crate\AddCrateCommand;
use Taco\ZP\command\crate\GiveKeyCommand;
use Taco\ZP\command\crate\KeyAllCommand;
use Taco\ZP\command\economy\BalanceCommand;
use Taco\ZP\command\economy\GiveMoneyCommand;
use Taco\ZP\command\gems\GemMenuCommand;
use Taco\ZP\command\gems\GiveGemCommand;
use Taco\ZP\command\random\CEMenuCommand;
use Taco\ZP\command\random\KitCommand;
use Taco\ZP\command\random\MineCommand;
use Taco\ZP\command\random\NightVisionCommand;
use Taco\ZP\command\random\TagCommand;
use Taco\ZP\command\staff\AddSellableCommand;
use Taco\ZP\command\staff\AddTagCommand;
use Taco\ZP\command\staff\DebugCommand;
use Taco\ZP\command\staff\FreezeCommand;
use Taco\ZP\command\progression\PrestigeCommand;
use Taco\ZP\command\progression\RankupCommand;
use Taco\ZP\command\random\SpawnCommand;
use Taco\ZP\command\staff\AddWarpCommand;
use Taco\ZP\command\staff\SetRankCommand;
use Taco\ZP\Core;
use Taco\ZP\command\random\WarpCommand;

class CommandManager {

    public static function init() : void {
        $disable = [
            "me",
            "kill"
        ];
        foreach ($disable as $dis) {
            Core::getInstance()->getServer()->getCommandMap()->unregister(Core::getInstance()->getServer()->getCommandMap()->getCommand($dis));
        }
        Core::getInstance()->getServer()->getCommandMap()->registerAll("ZPrisons", [
            new RankupCommand(),
            new PrestigeCommand(),
            new SpawnCommand(),
            new FreezeCommand(),
            new AddWarpCommand(),
            new WarpCommand(),
            new AddTagCommand(),
            new TagCommand(),
            new AddSellableCommand(),
            new DebugCommand(),
            new GemMenuCommand(),
            new GiveGemCommand(),
            new CECommand(),
            new BalanceCommand(),
            new GiveMoneyCommand(),
            new CEMenuCommand(),
            new GiveKeyCommand(),
            new KeyAllCommand(),
            new AddCrateCommand(),
            new KitCommand(),
            new NightVisionCommand(),
            new MineCommand(),
            new SetRankCommand()
        ]);
    }

}