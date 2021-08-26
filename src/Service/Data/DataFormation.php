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
        $name = $this->sanitizeData->sanitizeString($data->name);
        $slug = $obj->getName() === $name ? $obj->getSlug() : $this->sanitizeData->fullSanitize($data->name);

        dump($data->content);

        return ($obj)
            ->setName($name)
            ->setSlug($slug)
            ->setContent($data->content->html)
            ->setPrice($data->price)
        ;
    }
}