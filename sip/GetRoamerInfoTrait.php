<?php

namespace App\Traits;

use App\MsrnRange;
use Illuminate\Support\Facades\Cache;

trait GetRoamerInfoTrait
{
    public function getRoamingStatus($traffic_type, $calling, $called)
    {
        $roaming = 0;

        switch ($traffic_type) {
            case 'incoming':

                $prefix = substr($calling, 0, 4);
                if ($prefix == 9472 || $prefix == 9478) {
                    $roaming = 2;
                } else {
                    $isMsrn = $this->isMsrn($called);
                    if ($isMsrn) {
                        $roaming = 1;
                    }
                }

                break;
            case 'outgoing':

                if (substr($calling, 0, 3) != 947) {
                    $roaming = 1;
                }

                break;
        }

        return $roaming;
    }

    public function isMsrn($called)
    {
        $msrns = $this->getMsrnRanges();
        foreach ($msrns as $msrn) {
            if ($called >= $msrn->from_msisdn && $called <= $msrn->to_msisdn) return true;
        }

        return false;
    }

    public function getMsrnRanges()
    {
        return MsrnRange::select('from_msisdn', 'to_msisdn')->get();
    }
}
