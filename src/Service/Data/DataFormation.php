<?php


namespace App\Service\Data;


use App\Entity\Formation\FoFormation;
use App\Service\SanitizeData;

class DataFormation extends DataConstructor
{
    public function setData(FoFormation $obj, $data): FoFormation
    {
        $name = $this->sanitizeData->sanitizeString($data->name);

        return ($obj)
            ->setName($name)
            ->setContent($data->content->html)
        ;
    }
}