<?php

namespace App\Service\Data;

use App\Entity\Immo\ImBien;
use Exception;

class DataImmo extends DataConstructor
{
    /**
     * @throws Exception
     */
    public function setDataBien(ImBien $obj, $data)
    {
        $codeTypeAd     = $data->codeTypeAd;
        $codeTypeBien   = $data->codeTypeBien;
        $libelle        = $data->libelle;
        $codeTypeMandat = $data->codeTypeMandat;

        // validation des données
        $paramsToValidate = [
            ['type' => 'text',   'name' => 'codeTypeAd',      'value' => $codeTypeAd],
            ['type' => 'text',   'name' => 'codeTypeBien',    'value' => $codeTypeBien],
            ['type' => 'text',   'name' => 'libelle',         'value' => $libelle],
            ['type' => 'text',   'name' => 'codeTypeMandat',  'value' => $codeTypeMandat],
            ['type' => 'length', 'name' => 'libelle',         'value' => $libelle, 'min' => 0, 'max' => 64],
        ];
        $noErrors = $this->validator->validateCustom($paramsToValidate);
        if ($noErrors !== true) {
            return $noErrors;
        }

        // Création de l'objet
        return ($obj)
            ->setSlug(null)
            ->setCodeTypeAd((int) $codeTypeAd)
            ->setCodeTypeBien((int) $codeTypeBien)
            ->setLibelle(trim($libelle))
            ->setCodeTypeMandat($codeTypeMandat)
            ->setReference(substr(mb_strtoupper(uniqid().bin2hex(random_bytes(1))), 0, 10))
        ;
    }
}