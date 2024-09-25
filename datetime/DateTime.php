<?php

namespace app\datetime;

class DateTime
{
    function __construct($timeZone)
    {
        date_default_timezone_set($timeZone);
    }

    /** 
     *    return current date time
     *    @param string $format
     *    @return string   
     */
    public static function getCurrentDateTime(string $format) : string
    {
        $log_data = date($format);

        return date($format);
    }
}