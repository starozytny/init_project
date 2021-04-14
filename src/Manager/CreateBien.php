<?php


namespace App\Manager;


use App\Entity\Immo\ImAddress;
use App\Entity\Immo\ImAgency;
use App\Entity\Immo\ImBien;
use App\Entity\Immo\ImFeature;
use App\Entity\Immo\ImFinancial;
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
        $financial = new ImFinancial();
        $feature = new ImFeature();
        foreach($biens as $b){
            if($b->getRef() == $data->bien->ref && $agency->getId() == $b->getAgency()->getId()){
                $bien = $b;
                $address = $b->getAddress();
                $financial = $b->getFinancial();
                $feature = $b->getFeature();
            }
        }

        $feature = $this->createFeatureFromJson($feature, $data->features);
        $this->em->persist($feature);

        $financial = $this->createFinancialFromJson($financial, $data->financial);
        $this->em->persist($financial);

        $address = $this->createAddressFromJson($address, $data->address);
        $this->em->persist($address);

        $bien = $this->createBienFromJson($bien, $data->bien, $agency, $address, $financial, $feature);
        $this->em->persist($bien);

       return $bien;
    }

    /**
     * @throws Exception
     */
    private function createBienFromJson(ImBien $bien, $item, ImAgency $agency, ImAddress $address,
                                        ImFinancial $financial, ImFeature $feature): ImBien
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
            ->setFinancial($financial)
            ->setFeature($feature)
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

    private function createFinancialFromJson(ImFinancial $financial, $item): ImFinancial
    {
        return ($financial)
            ->setPrice($item->price)
            ->setCommission($item->commission)
            ->setCharges($item->charges)
            ->setFoncier($item->foncier)
            ->setDeposit($item->deposit)
            ->setPartHonoEdl($item->partHonoEdl)
            ->setComplementLoyer($item->complementLoyer)
            ->setHonoChargesDe($item->honoChargesDe)
            ->setHorsHonoAcquereur($item->horsHonoAcquereur)
            ->setModalitesChargesLocataire($item->modalitesChargesLocataire)
            ->setBouquet($item->bouquet)
            ->setRente($item->rente)
        ;
    }

    private function createFeatureFromJson(ImFeature $feature, $item): ImFeature
    {
        $feature = ($feature)
            ->setArea($item->area->area)
            ->setAreaLand($item->area->areaLand)
            ->setAreaLiving($item->area->areaLiving)
            ->setNbSdb($item->bathroom->nbSdb)
            ->setNbSle($item->bathroom->nbSle)
            ->setNbWc($item->bathroom->nbWc)
            ->setIsWcSeparate($item->bathroom->isWcSeparate)
            ->setFloor($item->floor->floor)
            ->setNbFloor($item->floor->nbFloor)
            ->setNbPiece($item->nbPiece)
            ->setNbRoom($item->nbRoom)
            ->setNbBalcony($item->nbBalcony)
            ->setIsMeuble($item->isMeuble)
            ->setConstructionYear($item->constructionYear)
            ->setIsRefaitNeuf($item->isRefaitNeuf)
            ->setHeating($item->heating)
            ->setKitchen($item->kitchen)
        ;

        if($item->exposition){
            $feature = ($feature)
                ->setIsSouth($item->exposition->isSouth)
                ->setIsEast($item->exposition->isEast)
                ->setIsWest($item->exposition->isWest)
                ->setIsNorth($item->exposition->isNorth)
            ;
        }

        return $feature;
    }
}