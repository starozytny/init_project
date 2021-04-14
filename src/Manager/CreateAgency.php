<?php


namespace App\Manager;


use App\Entity\Immo\ImAgency;

class CreateAgency
{
    public function createFromJson(ImAgency $agency, $data): ImAgency
    {
        return ($agency)
            ->setName($data->name)
            ->setDirname($data->dirname)
            ->setIdentifiant($data->identifiant)
            ->setDescription($data->description)
            ->setWebsite($data->website)
            ->setEmail($data->email)
            ->setEmailLocation($data->emailLocation)
            ->setEmailVente($data->emailVente)
            ->setPhone($data->phone)
            ->setPhoneLocation($data->phoneLocation)
            ->setPhoneVente($data->phoneVente)
            ->setLogo($data->logo)
            ->setTarif($data->tarif)
            ->setLegal($data->legal)
            ->setAddress($data->address)
            ->setZipcode($data->zipcode)
            ->setCity($data->city)
            ->setLat($data->lat)
            ->setLon($data->lon)
        ;
    }
}