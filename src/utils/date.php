<?php

namespace App\Utils;

use \Datetime;

/**
 * Description of DateUtil
 *
 * @author xE4
 */
class DateUtil {

    public static function timestamp() {
        $date = new DateTime();
        return $date->getTimestamp();
    }

    public static function dateYyyyMmDd() {
        return date('Y-m-d', DateUtil::timestamp());
    }

}
