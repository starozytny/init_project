<?php


namespace App\Manager;


use App\Entity\Immo\ImAgency;

class CreateAgency
{
    private function setDataCommon(ImAgency $agency, $data): ImAgency
    {
        return ($agency)
            ->setName($data->name)
            ->setDirname($data->dirname)
            ->setWebsite($data->website)
            ->setEmail($data->email)
            ->setEmailLocation($data->emailLocation)
            ->setEmailVente($data->emailVente)
            ->setPhone($data->phone)
            ->setPhoneLocation($data->phoneLocation)
            ->setPhoneVente($data->phoneVente)
            ->setAddress($data->address)
            ->setZipcode($data->zipcode)
            ->setCity($data->city)
            ->setLat($data->lat)
            ->setLon($data->lon)
        ;
    }

    public function createFromJson(ImAgency $agency, $data): ImAgency
    {
        $agency = $this->setDataCommon($agency, $data);
        return ($agency)
            ->setIdentifiant($data->identifiant)
            ->setDescription($data->description)
            ->setLegal($data->legal)
            ->setLogo($data->logo)
            ->setTarif($data->tarif)
        ;
    }

    public function setData(ImAgency $agency, $data): ImAgency
    {
        $agency = $this->setDataCommon($agency, $data);
        return ($agency)
            ->setIdentifiant($data->dirname)
            ->setDescription($data->description->value)
            ->setLegal($data->legal->value)
        ;
    }
}