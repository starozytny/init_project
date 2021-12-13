<?php


namespace App\Service\Data;


use App\Entity\Formation\FoFormation;
use App\Entity\Formation\FoSession;
use App\Entity\Formation\FoWorker;
use App\Entity\User;
use App\Service\SanitizeData;

class DataTeam extends DataConstructor
{
    public function setData(FoWorker $obj, $data, User $user): FoWorker
    {
        return ($obj)
            ->setFirstname(ucfirst(trim($data->firstname)))
            ->setLastname(mb_strtoupper(trim($data->lastname)))
            ->setType((int) $data->type)
            ->setUser($user)
        ;
    }
}