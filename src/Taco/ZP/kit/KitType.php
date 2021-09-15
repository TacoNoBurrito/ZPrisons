<?php namespace Taco\ZP\kit;

abstract class KitType {

    abstract function getName() : string;

    abstract function getItems() : array;

    abstract function getPermission() : string;

    abstract function getKitCooldown() : int;

    abstract function getCoolItemNames() : string;

}