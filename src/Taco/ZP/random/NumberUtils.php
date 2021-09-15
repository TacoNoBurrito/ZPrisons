<?php namespace Taco\ZP\random;

class NumberUtils {

    public function intToPrefix($input) : string {
        if (!is_numeric($input)) return "0";
        $suffixes = ["", "K", "M", "B", "T", "QD", "QT", "probsDuping"];
        $suffixIndex = 0;
        while(abs($input) >= 1000 && $suffixIndex < sizeof($suffixes)) {
            $suffixIndex++;
            $input /= 1000;
        }
        return ($input > 0 ? floor($input * 1000) / 1000 : ceil($input * 1000) / 1000) . $suffixes[$suffixIndex];
    }

}