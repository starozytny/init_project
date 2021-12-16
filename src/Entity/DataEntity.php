<?php

namespace App\Entity;

use Carbon\Carbon;
use Carbon\Factory;
use Exception;

class DataEntity
{
    public function initNewDate(): \DateTime
    {
        $createdAt = new \DateTime();
        $createdAt->setTimezone(new \DateTimeZone("Europe/Paris"));
        return $createdAt;
    }

    /**
     * @throws Exception
     */
    public function initToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * How long ago
     *
     * @param $date
     * @return string|null
     */
    public function getHowLongAgo($date): ?string
    {
        if($date){
            $frenchFactory = new Factory([
                'locale' => 'fr_FR',
                'timezone' => 'Europe/Paris'
            ]);
            $time = Carbon::instance($date);
            $time->subHours(1);

            return str_replace("dans", "il y a", $frenchFactory->make($time)->diffForHumans());
        }

        return null;
    }

    /**
     * return ll -> 5 janv. 2017
     * return LL -> 5 janvier 2017
     *
     * @param $date
     * @param string $format
     * @return string|null
     */
    public function getFullDateString($date, string $format = "ll"): ?string
    {
        if($date){
            $frenchFactory = new Factory([
                'locale' => 'fr_FR',
                'timezone' => 'Europe/Paris'
            ]);
            $time = Carbon::instance($date);
            $time->subHours(1);

            return $frenchFactory->make($time)->isoFormat($format);
        }

        return null;
    }

    /**
     * return format for new Date JS
     *
     * @param $date
     * @return string|null
     */
    public function setDateJavascript($date): ?string
    {
        date_default_timezone_set('Europe/Paris');
        return $date != null ? date_format($date, 'F d, Y H:i:s') : null;
    }
}