<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiTaxe;
use App\Entity\Society;
use App\Service\Data\DataConstructor;

class DataTaxe extends DataConstructor
{
    public function setData(BiTaxe $obj, $data, ?Society $society = null): BiTaxe
    {
        return ($obj)
            ->setSociety($society)

            ->setCode((int) $data->code)
            ->setRate((float) $data->rate)
        ;
    }
}
