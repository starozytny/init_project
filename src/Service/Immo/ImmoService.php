<?php


namespace App\Service\Immo;


use App\Entity\Immo\ImBien;

class ImmoService
{
    /**
     * @param $param -> locations / ventes
     * @return array
     */
    public function getTypeAdFormParamString($param): array
    {
        if($param === 'locations'){
            return [ImBien::NATURE_LOCATION, ImBien::NATURE_VACANCE];
        }

        return [ImBien::NATURE_VENTE, ImBien::NATURE_VIAGER,
            ImBien::NATURE_INVEST, ImBien::NATURE_PRESTIGE,
            ImBien::NATURE_BAIL, ImBien::NATURE_COMMERCE];
    }
}