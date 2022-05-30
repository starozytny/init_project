<?php

namespace App\Service\Bill;

use App\Entity\Bill\BiContract;

class ContractService
{
    /**
     * @param $month
     * @param BiContract[] $contracts
     * @return array
     */
    public function getData($month, array $contracts): array
    {
        $data = [];
        foreach($contracts as $elem){
            $period = $elem->getPeriod();

            switch ($month){
                case 1: // Janvier
                    $data[] = $elem;
                    break;
                case 4:
                case 8:
                case 12:
                    if($period == BiContract::PERIOD_MENS || $period == BiContract::PERIOD_TRIM){
                        $data[] = $elem;
                    }
                    break;
                case 7:
                    if($period == BiContract::PERIOD_MENS || $period == BiContract::PERIOD_TRIM || $period == BiContract::PERIOD_SEME){
                        $data[] = $elem;
                    }
                    break;
                default:
                    if($period == BiContract::PERIOD_MENS){
                        $data[] = $elem;
                    }
                    break;
            }
        }

        return $data;
    }
}