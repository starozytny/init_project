<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiAvoir;
use App\Entity\Bill\BiContract;
use App\Entity\Bill\BiContractCustomer;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiHistory;
use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiProduct;
use App\Entity\Bill\BiQuotation;
use App\Entity\Bill\BiSite;
use App\Entity\Bill\BiSociety;
use App\Service\Bill\BillService;
use App\Service\Data\DataConstructor;
use App\Service\SanitizeData;
use App\Service\ValidatorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DataBill extends DataConstructor
{
    private $billService;

    public function __construct(EntityManagerInterface $entityManager, ValidatorService $validatorService,
                                SanitizeData $sanitizeData, BillService $billService)
    {
        parent::__construct($entityManager, $validatorService, $sanitizeData);

        $this->billService = $billService;
    }

    public function setDataSociety(BiSociety $obj, $data): BiSociety
    {
        return ($obj)
            ->setCode((int) $data->code)
            ->setName(ucfirst($this->sanitizeData->sanitizeString($data->name)))
            ->setSiren($this->sanitizeData->trimData($data->siren))
            ->setSiret($this->sanitizeData->trimData($data->siret))
            ->setRcs($this->sanitizeData->trimData($data->rcs))
            ->setNumeroTva($this->sanitizeData->trimData($data->numeroTva))
            ->setForme($this->sanitizeData->setToZeroIfEmpty($data->forme))
            ->setEmail($this->sanitizeData->trimData($data->email))
            ->setPhone1($this->sanitizeData->trimData($data->phone1))
            ->setAddress($this->sanitizeData->trimData($data->address))
            ->setAddress2($this->sanitizeData->trimData($data->address2))
            ->setZipcode($this->sanitizeData->trimData($data->zipcode))
            ->setCity($this->sanitizeData->trimData($data->city))
            ->setComplement($this->sanitizeData->trimData($data->complement))
            ->setCountry($this->sanitizeData->trimData($data->country))
            ->setBankName($this->sanitizeData->trimData($data->bankName))
            ->setBankNumero($this->sanitizeData->trimData($data->bankNumero))
            ->setBankTitulaire($this->sanitizeData->trimData($data->bankTitulaire))
            ->setBankBic($this->sanitizeData->trimData($data->bankBic))
            ->setBankCode($this->sanitizeData->trimData($data->bankCode))
            ->setBankIban($this->sanitizeData->trimData($data->bankIban))
            ->setNoteQuotation($this->sanitizeData->trimData($data->noteQuotation))
            ->setFooterQuotation($this->sanitizeData->trimData($data->footerQuotation))
            ->setNoteInvoice($this->sanitizeData->trimData($data->noteInvoice))
            ->setFooterInvoice($this->sanitizeData->trimData($data->footerInvoice))
            ->setNoteAvoir($this->sanitizeData->trimData($data->noteAvoir))
            ->setFooterAvoir($this->sanitizeData->trimData($data->footerAvoir))
        ;
    }

    public function setFromInvoice($obj, BiSociety $society)
    {
        return ($obj)
            ->setFromName($society->getName())
            ->setFromAddress($society->getAddress())
            ->setFromAddress2($society->getAddress2())
            ->setFromComplement($society->getComplement())
            ->setFromZipcode($society->getZipcode())
            ->setFromCity($society->getCity())
            ->setFromCountry($society->getCountry())
            ->setFromEmail($society->getEmail())
            ->setFromPhone1($society->getPhone1())
            ->setFromSiren($society->getSiren())
            ->setFromTva($society->getNumeroTva())
            ->setLogo($this->billService->getLogoSociety($society))
        ;
    }

    public function setFromBank($obj, BiSociety $society)
    {
        return ($obj)
            ->setFromBankName($society->getBankName())
            ->setFromBankNumero($society->getBankNumero())
            ->setFromBankTitulaire($society->getBankTitulaire())
            ->setFromBankBic($society->getBankBic())
            ->setFromBankCode($society->getBankCode())
            ->setFromBankIban($society->getBankIban())
        ;
    }

    /**
     * @throws Exception
     */
    public function setCommonData($obj, $data, BiSociety $society)
    {
        $obj = $this->setFromInvoice($obj, $society);

        return ($obj)
            ->setSociety($society)
            ->setDateAt($this->sanitizeData->createDate($data->dateAt))

            ->setCustomerId($this->sanitizeData->setToKey($data->customer))
            ->setRefCustomer($this->sanitizeData->trimData($data->refCustomer))
            ->setToName($this->sanitizeData->trimData($data->toName))
            ->setToAddress($this->sanitizeData->trimData($data->toAddress))
            ->setToAddress2($this->sanitizeData->trimData($data->toAddress2))
            ->setToComplement($this->sanitizeData->trimData($data->toComplement))
            ->setToZipcode($this->sanitizeData->trimData($data->toZipcode))
            ->setToCity($this->sanitizeData->trimData($data->toCity))
            ->setToCountry($this->sanitizeData->trimData($data->toCountry))
            ->setToEmail($this->sanitizeData->trimData($data->toEmail))
            ->setToPhone1($this->sanitizeData->trimData($data->toPhone1))

            ->setSiteId($this->sanitizeData->setToKey($data->site))
            ->setRefSite($this->sanitizeData->trimData($data->refSite))
            ->setSiName($this->sanitizeData->trimData($data->siName))
            ->setSiAddress($this->sanitizeData->trimData($data->siAddress))
            ->setSiAddress2($this->sanitizeData->trimData($data->siAddress2))
            ->setSiComplement($this->sanitizeData->trimData($data->siComplement))
            ->setSiZipcode($this->sanitizeData->trimData($data->siZipcode))
            ->setSiCity($this->sanitizeData->trimData($data->siCity))
            ->setSiCountry($this->sanitizeData->trimData($data->siCountry))
            ->setSiEmail($this->sanitizeData->trimData($data->siEmail))
            ->setSiPhone1($this->sanitizeData->trimData($data->siPhone1))

            ->setTotalHt($this->sanitizeData->setToFloat($data->totalHt, 0))
            ->setTotalRemise($this->sanitizeData->setToFloat($data->totalRemise, 0))
            ->setTotalTva($this->sanitizeData->setToFloat($data->totalTva, 0))
            ->setTotalTtc($this->sanitizeData->setToFloat($data->totalTtc, 0))

            ->setNote($this->sanitizeData->trimData($data->note))
            ->setFooter($this->sanitizeData->trimData($data->footer))
            ;
    }

    /**
     * @throws Exception
     */
    public function setDataInvoiceFromContract(BiInvoice $obj, BiContractCustomer $relation, $refRelation, BiContract $contract, BiSociety $society): BiInvoice
    {
        /** @var BiInvoice $obj */
        $obj = $this->setFromInvoice($obj, $society);
        $obj = $this->setFromBank($obj, $society);

        $customer = $relation->getCustomer();

        if($site = $relation->getSite()){
            $obj = ($obj)
                ->setSiteId($this->sanitizeData->setToKey($site->getId()))
                ->setRefSite($this->sanitizeData->trimData($site->getNumero()))
                ->setSiName($this->sanitizeData->trimData($site->getName()))
                ->setSiAddress($this->sanitizeData->trimData($site->getAddress()))
                ->setSiAddress2($this->sanitizeData->trimData($site->getAddress2()))
                ->setSiComplement($this->sanitizeData->trimData($site->getComplement()))
                ->setSiZipcode($this->sanitizeData->trimData($site->getZipcode()))
                ->setSiCity($this->sanitizeData->trimData($site->getCity()))
                ->setSiCountry($this->sanitizeData->trimData($site->getCountry()))
                ->setSiEmail($this->sanitizeData->trimData($site->getEmail()))
                ->setSiPhone1($this->sanitizeData->trimData($site->getPhone()))
            ;
        }

        return ($obj)
            ->setSociety($society)
            ->setDueType($contract->getDueType())

            ->setCustomerId($this->sanitizeData->setToKey($customer->getId()))
            ->setRefCustomer($this->sanitizeData->trimData($customer->getId()))
            ->setToName($this->sanitizeData->trimData($customer->getName()))
            ->setToAddress($this->sanitizeData->trimData($customer->getAddress()))
            ->setToAddress2($this->sanitizeData->trimData($customer->getAddress2()))
            ->setToComplement($this->sanitizeData->trimData($customer->getComplement()))
            ->setToZipcode($this->sanitizeData->trimData($customer->getZipcode()))
            ->setToCity($this->sanitizeData->trimData($customer->getCity()))
            ->setToCountry($this->sanitizeData->trimData($customer->getCountry()))
            ->setToEmail($this->sanitizeData->trimData($customer->getEmail()))
            ->setToPhone1($this->sanitizeData->trimData($customer->getPhone()))

            ->setTotalHt($this->sanitizeData->setToFloat($contract->getTotalHt(), 0))
            ->setTotalRemise($this->sanitizeData->setToFloat($contract->getTotalRemise(), 0))
            ->setTotalTva($this->sanitizeData->setToFloat($contract->getTotalTva(), 0))
            ->setTotalTtc($this->sanitizeData->setToFloat($contract->getTotalTtc(), 0))
            ->setToPay($this->sanitizeData->setToFloat($contract->getTotalTtc(), 0))

            ->setNote($this->sanitizeData->trimData($contract->getNote()))
            ->setFooter($this->sanitizeData->trimData($contract->getFooter()))

            ->setContractId($contract->getId())
            ->setRefContract($contract->getNumero())
            ->setRelationId($relation->getId())
            ->setRefRelation($refRelation)
            ->setNumRelation($relation->getNumero())

            ->setDisplayBank($society->getBankName() || $society->getBankName())
        ;
    }

    /**
     * @throws Exception
     */
    public function setDataInvoice(BiInvoice $obj, $data, BiSociety $society): BiInvoice
    {
        /** @var BiInvoice $obj */
        $obj = $this->setFromBank($obj, $society);

        $obj = ($obj)
            ->setDueAt($this->sanitizeData->createDate($data->dueAt))
            ->setDueType((int) $data->dueType)
            ->setPayType((int) $data->payType)
            ->setQuotationId($this->sanitizeData->setToInteger($data->quotationId))
            ->setRefQuotation($this->sanitizeData->trimData($data->quotationRef))
            ->setDisplayBank($society->getBankName() || $society->getBankName())
            ->setToPay($this->sanitizeData->setToFloat($data->totalTtc, 0))
        ;

        return $this->setCommonData($obj, $data, $society);
    }

    /**
     * @throws Exception
     */
    public function setDataQuotation(BiQuotation $obj, $data, BiSociety $society): BiQuotation
    {
        $obj = ($obj)
            ->setValideTo($this->sanitizeData->createDate($data->valideTo))
        ;

        return $this->setCommonData($obj, $data, $society);
    }

    /**
     * @throws Exception
     */
    public function setDataAvoir(BiAvoir $obj, $data, BiSociety $society): BiAvoir
    {
        $obj = ($obj)
            ->setInvoiceId($this->sanitizeData->setToInteger($data->invoiceId))
            ->setRefInvoice($this->sanitizeData->trimData($data->invoiceRef))
        ;
        return $this->setCommonData($obj, $data, $society);
    }

    public function createNumero($page, $dateAt, BiSociety $society): ?string
    {
        $dateAtYear = $dateAt->format('Y');

        $prefix = null; $counter = null; $year = null;
        switch ($page){
            case "avoir":
                $prefix = BiAvoir::PREFIX;
                $counter = $society->getCounterAvoir();
                $year = $society->getYearAvoir();
                break;
            case "site":
                $prefix = BiSite::PREFIX;
                $counter = $society->getCounterSite();
                $year = $society->getYearSite();
                break;
            case "customer":
                $prefix = BiCustomer::PREFIX;
                $counter = $society->getCounterCustomer();
                $year = $society->getYearCustomer();
                break;
            case "contract":
                $prefix = BiContract::PREFIX;
                $counter = $society->getCounterContract();
                $year = $society->getYearContract();
                break;
            case "quotation":
                $prefix = BiQuotation::PREFIX;
                $counter = $society->getCounterQuotation();
                $year = $society->getYearQuotation();
                break;
            case "invoice":
                $prefix = BiInvoice::PREFIX;
                $counter = $society->getCounterInvoice();
                $year = $society->getYearInvoice();
                break;
            default:
                break;
        }

        if($year != null){
            if((int) $dateAtYear != $year){
                $counter = 0;

                $year = $dateAtYear;
            }

            $counterIncr = $counter + 1;

            switch ($page){
                case "avoir":
                    ($society)
                        ->setCounterAvoir($counterIncr)
                        ->setYearAvoir($year);
                    break;
                case "site":
                    ($society)
                        ->setCounterSite($counterIncr)
                        ->setYearSite($year);
                    break;
                case "customer":
                    ($society)
                        ->setCounterCustomer($counterIncr)
                        ->setYearCustomer($year);
                    break;
                case "contract":
                    ($society)
                        ->setCounterContract($counterIncr)
                        ->setYearContract($year);
                    break;
                case "quotation":
                    ($society)
                        ->setCounterQuotation($counterIncr)
                        ->setYearQuotation($year);
                    break;
                case "invoice":
                    ($society)
                        ->setCounterInvoice($counterIncr)
                        ->setDateInvoice($dateAt)
                        ->setYearInvoice($year);
                    break;
                default:
                    break;
            }

            return $this->billService->createNewNumero($counterIncr, $year, $prefix);
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function setDataInvoiceGenerated(BiInvoice $obj, $data): BiInvoice
    {
        $dateAt = $this->sanitizeData->createDate($data->dateAt);

        $numero = $this->createNumero("invoice", $dateAt, $obj->getSociety());
        return ($obj)
            ->setStatus(BiInvoice::STATUS_TO_PAY)
            ->setDateAt($dateAt)
            ->setDueAt($this->sanitizeData->createDate($data->dueAt))
            ->setDueType((int) $data->dueType)
            ->setNumero($numero)
            ;
    }

    /**
     * @throws Exception
     */
    public function setDataQuotationGenerated(BiQuotation $obj): BiQuotation
    {
        $numero = $this->createNumero("quotation", $obj->getDateAt(), $obj->getSociety());
        return ($obj)
            ->setStatus(BiQuotation::STATUS_PROCESSING)
            ->setNumero($numero)
        ;
    }

    /**
     * @throws Exception
     */
    public function setDataAvoirGenerated(BiAvoir $obj): BiAvoir
    {
        $numero = $this->createNumero("avoir", $obj->getDateAt(), $obj->getSociety());
        return ($obj)
            ->setStatus(BiAvoir::STATUS_ACTIF)
            ->setNumero($numero)
        ;
    }

    public function setDataProduct(BiProduct $obj, $data, BiSociety $society): BiProduct
    {
        return ($obj)
            ->setSociety($society)
            ->setUid($this->sanitizeData->trimData($data->uid))
            ->setReference($this->sanitizeData->trimData($data->reference))
            ->setNumero($this->sanitizeData->trimData($data->numero))
            ->setName($this->sanitizeData->trimData($data->name))
            ->setContent($this->sanitizeData->trimData($data->content))
            ->setUnity($this->sanitizeData->trimData($data->unity))
            ->setPrice($this->sanitizeData->setToFloat($data->price, 0))
            ->setRateTva($this->sanitizeData->setToFloat($data->rateTva, 0))
            ->setQuantity($this->sanitizeData->setToFloat($data->quantity, 0))
            ->setCodeTva($this->sanitizeData->setToFloat($data->codeTva, 0))
            ;
    }

    public function setDataCustomer(BiCustomer $obj, $data, BiSociety $society): BiCustomer
    {
        return ($obj)
            ->setSociety($society)
            ->setName($this->sanitizeData->trimData($data->name))
            ->setNumeroTva($this->sanitizeData->trimData($data->numeroTva))
            ->setEmail($this->sanitizeData->trimData($data->email))
            ->setPhone($this->sanitizeData->trimData($data->phone))
            ->setAddress($this->sanitizeData->trimData($data->address))
            ->setAddress2($this->sanitizeData->trimData($data->address2))
            ->setComplement($this->sanitizeData->trimData($data->complement))
            ->setZipcode($this->sanitizeData->trimData($data->zipcode))
            ->setCity($this->sanitizeData->trimData($data->city))
            ->setCountry($this->sanitizeData->trimData($data->country))
            ->setPayType((int) $data->payType)
            ;
    }

    public function setDataSite(BiSite $obj, $data, BiCustomer $customer, BiSociety $society): BiSite
    {
        return ($obj)
            ->setSociety($society)
            ->setCustomer($customer)
            ->setNumero($this->sanitizeData->trimData($data->numero))
            ->setName($this->sanitizeData->trimData($data->name))
            ->setAddress($this->sanitizeData->trimData($data->address))
            ->setAddress2($this->sanitizeData->trimData($data->address2))
            ->setComplement($this->sanitizeData->trimData($data->complement))
            ->setZipcode($this->sanitizeData->trimData($data->zipcode))
            ->setCity($this->sanitizeData->trimData($data->city))
            ->setCountry($this->sanitizeData->trimData($data->country))
            ->setEmail($this->sanitizeData->trimData($data->email))
            ->setPhone($this->sanitizeData->trimData($data->phone))
            ->setPayType((int) $data->payType)
            ;
    }

    /**
     * @throws Exception
     */
    public function setDataHistory(BiHistory $obj, BiInvoice $invoice, $type, $name, DateTime $date, $price = null): BiHistory
    {
        return ($obj)
            ->setInvoice($invoice)
            ->setType($type)
            ->setName($name)
            ->setDateAt($date)
            ->setPrice($price)
            ;
    }
}