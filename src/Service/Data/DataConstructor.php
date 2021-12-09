<?php

namespace App\Service\Data;

use App\Service\ValidatorService;

class DataConstructor
{
    protected $validator;

    public function __construct(ValidatorService $validator)
    {
        $this->validator = $validator;
    }
}