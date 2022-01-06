<?php


namespace App\Service\Data\Agenda;


use App\Entity\Agenda\AgEvent;
use App\Service\Data\DataConstructor;
use Exception;

class DataEvent extends DataConstructor
{
    /**
     * @throws Exception
     */
    public function setDataEvent(AgEvent $obj, $data): AgEvent
    {
        $persons = [];
        $persons["users"] = $data->users ?: [];

        return ($obj)
            ->setName($this->sanitizeData->sanitizeString($data->name))
            ->setStartAt($this->createDate($data->startAt))
            ->setEndAt($this->createDate($data->endAt))
            ->setAllDay((int) $data->allDay[0])
            ->setLocation($this->sanitizeData->trimData($data->location))
            ->setComment($this->sanitizeData->trimData($data->comment))
            ->setStatus((int) $data->status)
            ->setVisibilities($data->visibilities)
            ->setPersons($persons)
        ;
    }

    /**
     * @throws Exception
     */
    public function setDataEventDate(AgEvent $obj, $data): AgEvent
    {
        return ($obj)
            ->setStartAt($this->createDate($data->startAt))
            ->setEndAt($this->createDate($data->endAt))
        ;
    }
}