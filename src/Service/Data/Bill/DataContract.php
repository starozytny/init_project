<?php

namespace App\Service\Data\Bill;

use App\Entity\Bill\BiContract;
use App\Entity\Bill\BiContractCustomer;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiSite;
use App\Entity\Bill\BiSociety;
use App\Service\Data\DataConstructor;
use Exception;

class DataContract extends DataConstructor
{
    /**
     * @throws Exception
     */
    public function setDataContract(BiContract $obj, $data, BiSociety $society): BiContract
    {
        $dateAt = $this->sanitizeData->createDate($data->dateAt);
        $dateAt->modify('first day of this month');

        return ($obj)
            ->setSociety($society)
            ->setName($this->sanitizeData->trimData($data->name))
            ->setTheme($this->sanitizeData->setToInteger($data->theme, BiInvoice::THEME_1))
            ->setPeriod($this->sanitizeData->setToKey($data->period, BiContract::PERIOD_ANNU))
            ->setDateAt($dateAt)
            ->setDueAt($dateAt)
            ->setDueType((int) $data->dueType)
            ->setDuration($this->sanitizeData->setToInteger($data->duration))
            ->setTotalHt($this->sanitizeData->setToFloat($data->totalHt, 0))
            ->setTotalRemise($this->sanitizeData->setToFloat($data->totalRemise, 0))
            ->setTotalTva($this->sanitizeData->setToFloat($data->totalTva, 0))
            ->setTotalTtc($this->sanitizeData->setToFloat($data->totalTtc, 0))
            ->setNote($this->sanitizeData->trimData($data->note))
            ->setFooter($this->sanitizeData->trimData($data->footer))
        ;
    }

    public function setDataRelation(BiContractCustomer $obj, $data, BiContract $contract, BiCustomer $customer, ?BiSite $site): BiContractCustomer
    {
        return ($obj)
            ->setContract($contract)
            ->setCustomer($customer)
            ->setSite($site)
        ;
    }
}
