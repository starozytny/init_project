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
            ->setSlug(null)
            ->setContent(trim($data->content->html))
            ->setPrerequis(trim($data->prerequis->html))
            ->setGoals(trim($data->goals->html))
            ->setAptitudes(trim($data->aptitudes->html))
            ->setSkills(trim($data->skills->html))
            ->setTarget(trim($data->target->html))
            ->setCat(trim($data->cat->html))
            ->setAccessibility((int) $data->accessibility)
        ;
    }
}