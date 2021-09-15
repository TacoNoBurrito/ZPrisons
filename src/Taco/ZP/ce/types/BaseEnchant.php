<?php namespace Taco\ZP\ce\types;

use pocketmine\item\enchantment\Enchantment;

abstract class BaseEnchant extends Enchantment {

    abstract function getListedRarity() : int;

    abstract function getListedName() : string;

    abstract function getDescription() : string;

}