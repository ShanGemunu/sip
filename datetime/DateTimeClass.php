<?php

namespace app\datetime;

class DateTimeClass
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

        return date($format);
    }
}