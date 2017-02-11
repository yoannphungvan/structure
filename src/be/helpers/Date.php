<?php
/**
 * Created by PhpStorm.
 * User: vanyaonn
 * Date: 15-10-29
 * Time: 12:20 AM
 */

namespace PROJECT\Helpers;

class DateHelper
{
    public static function getDiffDate($date1, $date2)
    {
        return strtotime($date2) - strtotime($date1);
    }

    public static function getNow()
    {
        return date('Y-m-d H:i:s');
    }

    public static function addSecondToDate($date, $seconds)
    {
        $date = new \DateTime($date);
        $date->modify('+' . $seconds .' second');
        return $date->format('Y-m-d H:i:s');
    }
}
