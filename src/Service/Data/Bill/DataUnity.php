<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiUnity;
use App\Entity\Bill\BiSociety;
use App\Service\Data\DataConstructor;

class DataUnity extends DataConstructor
{
    public function setData(BiUnity $obj, $data, ?BiSociety $society = null): BiUnity
    {
        return ($obj)
            ->setSociety($society)

            ->setName($this->sanitizeData->trimData($data->name))
        ;
    }
}
