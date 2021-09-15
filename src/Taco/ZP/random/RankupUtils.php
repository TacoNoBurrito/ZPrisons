<?php namespace Taco\ZP\random;

class RankupUtils {

    public function num2Letter(int $num) : string {
        return chr(substr("000".($num + 65),-3));
    }

    public function letter2Num(string $letters) {
        $alphabet = range("A", "Z");
        $number = 0;
        foreach(str_split(strrev($letters)) as $key => $char){
            $number = $number + (array_search($char, $alphabet)) * pow(count($alphabet), $key);
        }
        return $number;
    }

}