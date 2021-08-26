<?php


namespace App\Service\Data;


use App\Entity\Formation\FoFormation;
use App\Service\SanitizeData;

class DataFormation
{
    private $sanitizeData;

    public function __construct(SanitizeData $sanitizeData)
    {
        $this->sanitizeData = $sanitizeData;
    }
    public function setData(FoFormation $obj, $data): FoFormation
    {
        return ($obj)
            ->setName($this->sanitizeData->sanitizeString($data->name))
        ;
    }
}