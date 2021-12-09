<?php

namespace App\Service\Data;

use App\Service\SanitizeData;
use App\Service\ValidatorService;

class DataConstructor
{
    protected $validator;
    protected $sanitizeData;

    public function __construct(ValidatorService $validator, SanitizeData $sanitizeData)
    {
        $this->validator = $validator;
        $this->sanitizeData = $sanitizeData;
    }
}