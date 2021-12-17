<?php

namespace App\Service\Data;

use App\Service\SanitizeData;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;

class DataConstructor
{
    protected $validator;
    protected $em;
    protected $sanitizeData;

    public function __construct(EntityManagerInterface $entityManager, ValidatorService $validator, SanitizeData $sanitizeData)
    {
        $this->validator = $validator;
        $this->em = $entityManager;
        $this->sanitizeData = $sanitizeData;
    }
}