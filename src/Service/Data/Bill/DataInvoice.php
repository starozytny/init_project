<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiInvoice;
use App\Service\Data\DataConstructor;

class DataInvoice extends DataConstructor
{
    public function setDataInvoice(BiInvoice $obj, $data): BiInvoice
    {
        return ($obj)
            ->setFromName($this->sanitizeData->trimData($data->fromName))
            ->setFromAddress($this->sanitizeData->trimData($data->fromAddress))
            ->setFromComplement($this->sanitizeData->trimData($data->fromComplement))
            ->setFromZipcode($this->sanitizeData->trimData($data->fromZipcode))
            ->setFromCity($this->sanitizeData->trimData($data->fromCity))
            ->setFromEmail($this->sanitizeData->trimData($data->fromEmail))
            ->setFromPhone1($this->sanitizeData->trimData($data->fromPhone1))
            ->setToName($this->sanitizeData->trimData($data->toName))
            ->setToAddress($this->sanitizeData->trimData($data->toAddress))
            ->setToComplement($this->sanitizeData->trimData($data->toComplement))
            ->setToZipcode($this->sanitizeData->trimData($data->toZipcode))
            ->setToCity($this->sanitizeData->trimData($data->toCity))
            ->setToEmail($this->sanitizeData->trimData($data->toEmail))
            ->setToPhone1($this->sanitizeData->trimData($data->toPhone1))
        ;
    }
}
