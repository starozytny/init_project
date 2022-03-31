<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiItem;
use App\Entity\Society;
use App\Service\Bill\BillService;
use App\Service\Data\DataConstructor;
use App\Service\SanitizeData;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DataItem extends DataConstructor
{
    /**
     * @throws Exception
     */
    public function setData(BiItem $obj, $data, Society $society): BiItem
    {
        return ($obj)
            ->setSociety($society)

            ->setReference($this->sanitizeData->trimData($data->reference))
            ->setNumero($this->sanitizeData->trimData($data->numero))

            ->setName($this->sanitizeData->trimData($data->name))
            ->setContent($this->sanitizeData->trimData($data->content))

            ->setUnity($this->sanitizeData->setToInteger($data->unity, 0))
            ->setPrice($this->sanitizeData->setToFloat($data->price))
            ->setRateTva($this->sanitizeData->setToFloat($data->rateTva, 0))
        ;
    }
}
