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

    public function setDataInvoice(BiInvoice $obj, $data, Society $society): BiInvoice
    {
        return ($obj)
            ->setFromName($society->getName())
            ->setFromAddress($society->getAddress())
            ->setFromComplement($society->getComplement())
            ->setFromZipcode($society->getZipcode())
            ->setFromCity($society->getCity())
            ->setFromEmail($society->getEmail())
            ->setFromPhone1($society->getPhone1())
            ->setFromSiren($society->getSiren())
            ->setFromTva($society->getNumeroTva())

            ->setToName($this->sanitizeData->trimData($data->toName))
            ->setToAddress($this->sanitizeData->trimData($data->toAddress))
            ->setToComplement($this->sanitizeData->trimData($data->toComplement))
            ->setToZipcode($this->sanitizeData->trimData($data->toZipcode))
            ->setToCity($this->sanitizeData->trimData($data->toCity))
            ->setToEmail($this->sanitizeData->trimData($data->toEmail))
            ->setToPhone1($this->sanitizeData->trimData($data->toPhone1))

            ->setTotalHt($this->sanitizeData->setToFloat($data->totalHt, 0))
            ->setTotalRemise($this->sanitizeData->setToFloat($data->totalRemise, 0))
            ->setTotalTva($this->sanitizeData->setToFloat($data->totalTva, 0))
            ->setTotalTtc($this->sanitizeData->setToFloat($data->totalTtc, 0))

            ->setNote($this->sanitizeData->trimData($data->note))
            ->setFooter($this->sanitizeData->trimData($data->footer))
            ->setLogo($this->sanitizeData->trimData($data->logo))
        ;
    }

    public function createNumero($dateAt, Society $society): string
    {
        $nowYear = $dateAt->format('y');

        $counter = $society->getCounterInvoice();
        $year = $society->getYearInvoice();
        if((int) $nowYear != $year){
            $counter = 0;

            $year = $nowYear;
            $society->setYearInvoice($year);
        }

        $counterInvoice = $counter + 1;

        ($society)
            ->setCounterInvoice($counterInvoice)
            ->setDateInvoice($dateAt)
        ;

        return $this->billService->createNewNumero($counterInvoice, $year, BiInvoice::PREFIX);
    }
}
