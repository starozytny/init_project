<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiInvoice;
use App\Entity\Society;
use App\Service\Bill\BillService;
use App\Service\Data\DataConstructor;
use App\Service\SanitizeData;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;

class DataInvoice extends DataConstructor
{
    private $billService;

    public function __construct(EntityManagerInterface $entityManager, ValidatorService $validatorService, SanitizeData $sanitizeData,
                                BillService $billService)
    {
        parent::__construct($entityManager, $validatorService, $sanitizeData);

        $this->billService = $billService;
    }
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
            ->setFromSiren($this->sanitizeData->trimData($data->fromSiren))
            ->setFromTva($this->sanitizeData->trimData($data->fromTva))

            ->setToName($this->sanitizeData->trimData($data->toName))
            ->setToAddress($this->sanitizeData->trimData($data->toAddress))
            ->setToComplement($this->sanitizeData->trimData($data->toComplement))
            ->setToZipcode($this->sanitizeData->trimData($data->toZipcode))
            ->setToCity($this->sanitizeData->trimData($data->toCity))
            ->setToEmail($this->sanitizeData->trimData($data->toEmail))
            ->setToPhone1($this->sanitizeData->trimData($data->toPhone1))

            ->setTotalHt($this->sanitizeData->setToFloat($data->totalHt, 0))
            ->setTotalTva($this->sanitizeData->setToFloat($data->totalTva, 0))
            ->setTotalTtc($this->sanitizeData->setToFloat($data->totalTtc, 0))

            ->setNote($this->sanitizeData->trimData($data->note))
            ->setLogo($this->sanitizeData->trimData($data->logo))
        ;
    }

    public function createNumeroInvoice(Society $society): string
    {
        $counter = $society->getCounterBill() + 1;
        $society->setCounterBill($counter);

        return $this->createNumero($counter);
    }

    public function createNumero($i): string
    {
        return $this->billService->createNewNumero($i, BiInvoice::PREFIX);
    }
}
