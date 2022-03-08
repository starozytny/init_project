<?php


namespace App\Service\Data;


use App\Entity\Society;
use App\Entity\User;
use App\Service\SanitizeData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DataUser
{
    private $sanitizeData;

    public function __construct(SanitizeData $sanitizeData)
    {
        $this->sanitizeData = $sanitizeData;
    }

    public function setData(User $obj, $data): User
    {
        if (isset($data->roles)) {
            $obj->setRoles($data->roles);
        }

        $society = $this->em->getRepository(Society::class)->find($data->society);

        $username = isset($data->username) ? $this->sanitizeData->fullSanitize($data->username) : $data->email;

        return ($obj)
            ->setUsername($username)
            ->setFirstname(ucfirst($this->sanitizeData->sanitizeString($data->firstname)))
            ->setLastname(mb_strtoupper($this->sanitizeData->sanitizeString($data->lastname)))
            ->setEmail($data->email)
        ;
    }
}