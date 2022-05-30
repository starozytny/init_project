<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiTaxe;
use App\Entity\Bill\BiSociety;
use App\Service\SanitizeData;

class DataTaxe
{
    private $sanitizeData;

    public function __construct(SanitizeData $sanitizeData)
    {

        $this->sanitizeData = $sanitizeData;
    }

    public function setData(BiTaxe $obj, $data, ?BiSociety $society = null): BiTaxe
    {
        return ($obj)
            ->setSociety($society)

            ->setCode((int) $data->code)
            ->setRate((float) $data->rate)
            ->setNumeroComptable($this->sanitizeData->trimData($data->numeroComptable))
        ;
    }
}
