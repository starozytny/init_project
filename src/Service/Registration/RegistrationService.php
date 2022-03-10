<?php

namespace App\Service\Registration;

use App\Entity\Formation\FoRegistration;
use App\Entity\Paiement\PaOrder;

class RegistrationService
{
    /**
     * @param FoRegistration[] $registrations
     * @return void
     */
    public function cancelRegistrationsFromOrder(array $registrations, PaOrder $order)
    {
        foreach($registrations as $registration){
            if($registration->getPaOrder()->getId() == $order->getId()){
                $registration->setStatus(FoRegistration::STATUS_INACTIVE);
            }
        }
    }
}
