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
    private $publicDirectory;

    public function __construct(EntityManagerInterface $entityManager, ValidatorService $validatorService, SanitizeData $sanitizeData,
                                $publicDirectory, BillService $billService)
    {
        parent::__construct($entityManager, $validatorService, $sanitizeData);

        $this->billService = $billService;
        $this->publicDirectory = $publicDirectory;
    }

    /**
     * @return mixed
     */
    public function getPublicDirectory()
    {
        return $this->publicDirectory;
    }

    private function getLogoSociety(Society $society): ?string
    {
        if($society->getLogo()){
            $file = $this->getPublicDirectory() . Society::FOLDER_LOGOS . '/' . $society->getLogo();

            if(file_exists($file)){
                $data = file_get_contents($file);
                $extension = pathinfo($file, PATHINFO_EXTENSION);;

                return 'data:image/' . $extension . ';base64,' . base64_encode($data);
            }
        }

        return null;
    }

    public function setDataInvoice(BiInvoice $obj, $data, Society $society): BiInvoice
    {
        return ($obj)
            ->setSociety($society)
            ->setDateAt(new \DateTime())

            ->setFromName($society->getName())
            ->setFromAddress($society->getAddress())
            ->setFromComplement($society->getComplement())
            ->setFromZipcode($society->getZipcode())
            ->setFromCity($society->getCity())
            ->setFromEmail($society->getEmail())
            ->setFromPhone1($society->getPhone1())
            ->setFromSiren($society->getSiren())
            ->setFromTva($society->getNumeroTva())
            ->setLogo($this->getLogoSociety($society))

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
