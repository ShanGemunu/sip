<?php

namespace app\logic;

use DateTime;

class Logic
{
    public static function findDateDifferenceData(string $currentDate, string $recordDate): array
    {
        $date1 = new DateTime($currentDate);
        $date2 = new DateTime($recordDate);

        $interval = $date1->diff($date2);
        //  if date1 > date2   1, otherwise 0 for $interval->invert

        return ['diff' => $interval->days, 'direction' => $interval->invert];
    }
}