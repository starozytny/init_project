<?php


namespace App\Service\Data;


use App\Entity\Formation\FoFormation;
use App\Entity\Formation\FoSession;
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

    public function setDataSession(FoSession $obj, $data): FoSession
    {
        $animator = $this->sanitizeData->sanitizeString($data->animator);

        return ($obj)
            ->setAnimator($animator)
            ->setType((int) $data->type)
            ->setDuration(trim($data->duration))
            ->setDuration2(trim($data->duration2))
            ->setDurationTotal(trim($data->durationTotal))
            ->setDurationByDay(trim($data->durationByDay))
            ->setPriceHT((float) $data->priceHt)
            ->setPriceTTC((float) $data->priceTtc)
            ->setTva((float) $data->tva)
            ->setMin((int) $data->min)
            ->setMax((int) $data->max)
            ->setAddress(trim($data->address))
            ->setZipcode(trim($data->zipcode))
            ->setCity(trim($data->city))
            ->setModTrav(trim($data->modTrav->html))
            ->setModEval(trim($data->modEval->html))
            ->setModPeda(trim($data->modPeda->html))
            ->setModAssi(trim($data->modAssi->html))
            ;
    }
}