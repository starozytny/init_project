<?php


namespace App\Service\Data\Society;


use App\Entity\Society;
use App\Service\SanitizeData;

class DataSociety
{
    private $sanitizeData;

    public function __construct(SanitizeData $sanitizeData)
    {
        $this->sanitizeData = $sanitizeData;
    }

    public function setData(Society $obj, $data, $code): Society
    {
        return ($obj)
            ->setCode($code)
            ->setName(ucfirst($this->sanitizeData->sanitizeString($data->name)))
            ->setSiren($this->sanitizeData->trimData($data->siren))
            ->setSiret($this->sanitizeData->trimData($data->siret))
            ->setRcs($this->sanitizeData->trimData($data->rcs))
            ->setNumeroTva($this->sanitizeData->trimData($data->numeroTva))
            ->setForme($this->sanitizeData->setToZeroIfEmpty($data->forme))
            ->setEmail($this->sanitizeData->trimData($data->email))
            ->setPhone1($this->sanitizeData->trimData($data->phone1))
            ->setAddress($this->sanitizeData->trimData($data->address))
            ->setZipcode($this->sanitizeData->trimData($data->zipcode))
            ->setCity($this->sanitizeData->trimData($data->city))
            ->setComplement($this->sanitizeData->trimData($data->complement))
            ->setCountry($this->sanitizeData->trimData($data->country))
            ->setBankName($this->sanitizeData->trimData($data->bankName))
            ->setBankNumero($this->sanitizeData->trimData($data->bankNumero))
            ->setBankTitulaire($this->sanitizeData->trimData($data->bankTitulaire))
            ->setBankBic($this->sanitizeData->trimData($data->bankBic))
            ->setBankCode($this->sanitizeData->trimData($data->bankCode))
            ->setBankIban($this->sanitizeData->trimData($data->bankIban))
        ;
    }
}
