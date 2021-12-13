<?php


namespace App\Service\Data\Paiement;


use App\Entity\Paiement\PaBank;
use App\Entity\User;

class DataBank
{
    public function setData(PaBank $obj, $data, User $user): PaBank
    {
        return ($obj)
            ->setTitulaire(trim($data->titulaire))
            ->setIban(trim($data->iban))
            ->setBic(trim($data->bic))
            ->setUser($user)
        ;
    }
}