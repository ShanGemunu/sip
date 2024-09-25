<?php

namespace app\logic;

use DateTime;

class Logic
{
    function findDateDifference(string $currentDate, string $recordDate)
    {
        $date1 = new DateTime($currentDate);
        $date2 = new DateTime($recordDate);
        
        //  if date1 > date2   1, otherwise 0
        $interval = $date1->diff($date2);
        
        echo "Days difference: " . $interval->days . "\n";  // Total difference in days
        echo "Invert value: " . $interval->invert . "\n";   // Direction of the difference
    }
}