<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiTaxe;
use App\Entity\Society;

class DataTaxe
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
