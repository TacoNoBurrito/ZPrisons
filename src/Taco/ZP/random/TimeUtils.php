<?php namespace Taco\ZP\random;

use UnexpectedValueException;
use function count;
use function floor;

class TimeUtils {

    public const TIME_DAY = 86400;
    public const TIME_HOUR  = 3600;

    public static function intToTimeString(int $seconds) : string {
        if($seconds < 0) throw new UnexpectedValueException("time can't be a negative value");
        if($seconds === 0) {
            return "0 seconds";
        }
        $timeString = "";
        $timeArray = [];
        if($seconds >= 86400) {
            $unit = floor($seconds / 86400);
            $seconds -= $unit * 86400;
            $timeArray[] = $unit . " days";
        }
        if($seconds >= 3600) {
            $unit = floor($seconds / 3600);
            $seconds -= $unit * 3600;
            $timeArray[] = $unit . " hours";
        }
        if($seconds >= 60) {
            $unit = floor($seconds / 60);
            $seconds -= $unit * 60;
            $timeArray[] = $unit . " minutes";
        }
        if($seconds >= 1) {
            $timeArray[] = $seconds . " seconds";
        }
        foreach($timeArray as $key => $value) {
            if($key === 0) {
                $timeString .= $value;
            } elseif($key === count($timeArray) - 1) {
                $timeString .= " and " . $value;
            } else {
                $timeString .= ", " . $value;
            }
        }
        return $timeString;
    }

}