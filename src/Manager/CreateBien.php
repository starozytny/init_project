<?php


namespace App\Manager;


use App\Entity\Immo\ImAddress;
use App\Entity\Immo\ImAgency;
use App\Entity\Immo\ImBien;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateBien
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->em = $entityManager;
    }
    /**
     * @throws Exception
     */
    public function createFromJson($data, $biens, ImAgency $agency): ImBien
    {
        /** @var ImBien $b */
        $bien = new ImBien();
        $address = new ImAddress();
        foreach($biens as $b){
            if($b->getRef() == $data->bien->ref && $agency->getId() == $b->getAgency()->getId()){
                $bien = $b;
                $address = $b->getAddress();
            }
        }

        $address = $this->createAddressFromJson($address, $data->address);
        $this->em->persist($address);

        $bien = $this->createBienFromJson($bien, $data->bien, $agency, $address);
        $this->em->persist($bien);

       return $bien;
    }

    /**
     * @throws Exception
     */
    private function createBienFromJson(ImBien $bien, $item, ImAgency $agency, ImAddress $address): ImBien
    {
        return ($bien)
            ->setRef($item->ref)
            ->setRealRef($item->realRef)
            ->setDispo($item->dispo ? new DateTime($item->dispo) : null)
            ->setTypeAd($item->typeAd)
            ->setTypeBien($item->typeBien)
            ->setCodeTypeAd($item->codeTypeAd)
            ->setCodeTypeBien($item->codeTypeBien)
            ->setTypeT($item->typeT)
            ->setLabel($item->label)
            ->setContent($item->content)
            ->setAgency($agency)
            ->setAddress($address)
            ->setIsSync(true)
        ;
    }

    private function createAddressFromJson(ImAddress $address, $item): ImAddress
    {
        return ($address)
            ->setAddress($item->address)
            ->setZipcode($item->zipcode)
            ->setCity($item->city)
            ->setDistrict($item->district)
            ->setArdt($item->ardt)
            ->setLat($item->lat)
            ->setLon($item->lon)
        ;
    }
}