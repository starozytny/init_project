<?php

namespace App\Service\Data;

class DataPaiement
{
    private $privateDirectory;

    public function __construct($privateDirectory)
    {
        $this->privateDirectory = $privateDirectory;
    }

    public function getFile($filename): string
    {
        $path = $this->getPrivateDirectory() . "/paiements";
        return $path . "/" .$filename;
    }

    public function getPaiementDirectory(): string
    {
        return $this->getPrivateDirectory() . "/paiements";
    }

    protected function getPrivateDirectory()
    {
        return $this->privateDirectory;
    }
}