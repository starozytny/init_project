<?php


namespace App\Manager;


use App\Entity\Immo\ImAddress;
use App\Entity\Immo\ImAgency;
use App\Entity\Immo\ImBien;
use App\Entity\Immo\ImCopro;
use App\Entity\Immo\ImDiagnostic;
use App\Entity\Immo\ImFeature;
use App\Entity\Immo\ImFeatureExt;
use App\Entity\Immo\ImFinancial;
use App\Entity\Immo\ImResponsable;
use App\Service\SanitizeData;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateBien
{
    private $em;
    private $sanitizeData;

    public function __construct(EntityManagerInterface $entityManager, SanitizeData $sanitizeData)
    {
        $this->em = $entityManager;
        $this->sanitizeData = $sanitizeData;
    }
    /**
     * @throws Exception
     */
    public function createFromJson($data, $biens, ImAgency $agency): ImBien
    {
        /** @var ImBien $b */
        $bien = null;
        $address = new ImAddress();
        $financial = new ImFinancial();
        $feature = new ImFeature();
        $featureExt = null;
        $diagnostic = null;
        $copro = null;
        $responsable = null;
        $images = [];
        $identifiant = uniqid().bin2hex(random_bytes(8));
        foreach($biens as $b){
            if($bien == null && $agency->getId() == $b->getAgency()->getId()){
                if($b->getRealRef()){
                    if($b->getRealRef() == $data->bien->realRef){
                        $bien = $b;
                    }
                }else{
                    if($b->getRef() == $data->bien->ref){
                        $bien = $b;
                    }
                }

                if($bien != null){
                    $address = $b->getAddress();
                    $financial = $b->getFinancial();
                    $feature = $b->getFeature();
                    $featureExt = $b->getFeatureExt();
                    $diagnostic = $b->getDiagnostic();
                    $copro = $b->getCopro();
                    $responsable = $b->getResponsable();
                    $images = $b->getImages();
                    $identifiant = $b->getIdentifiant();
                }
            }
        }

        if(!$bien){
            $bien = new ImBien();
        }

        $this->deleteImages($images);

        $responsable = $this->createResponsableFromJson($responsable, $data->responsable);
        if($responsable){ $this->em->persist($responsable); }

        $copro = $this->createCoproFromJson($copro, $data->copro);
        if($copro){ $this->em->persist($copro); }

        $diagnostic = $this->createDiagnosticFromJson($diagnostic, $data->diagnostic);
        if($diagnostic){ $this->em->persist($diagnostic); }

        $featureExt = $this->createFeatureExtFromJson($featureExt, $data->featuresExt);
        if($featureExt){ $this->em->persist($featureExt); }

        $feature = $this->createFeatureFromJson($feature, $data->features);
        $this->em->persist($feature);

        $financial = $this->createFinancialFromJson($financial, $data->financial);
        $this->em->persist($financial);

        $address = $this->createAddressFromJson($address, $data->address);
        $this->em->persist($address);

        $bien = $this->createBienFromJson($bien, $data->bien, $identifiant, $agency, $address, $financial, $feature,
                                          $featureExt, $diagnostic, $copro);
        $this->em->persist($bien);

       return $bien;
    }

    /**
     * @throws Exception
     */
    private function createBienFromJson(ImBien $bien, $item, $identifiant, ImAgency $agency, ImAddress $address,
                                        ImFinancial $financial, ImFeature $feature, ?ImFeatureExt $featureExt,
                                        ?ImDiagnostic $diagnostic, ?ImCopro $copro): ImBien
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
            ->setFeatureExt($featureExt)
            ->setDiagnostic($diagnostic)
            ->setCopro($copro)
            ->setIsSync(true)
            ->setIdentifiant($identifiant)
            ->setVirtuel($item->virtual)
            ->setPanoramique($item->panoramique)
            ->setLibelle($item->libelle)
            ->setIdentifiantTech($item->identifiantTech)
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

    private function createFeatureExtFromJson(?ImFeatureExt $featureExt, $item): ?ImFeatureExt
    {
        if($item){
            $featureExt = $featureExt ?? new ImFeatureExt();
            if($item->parking != null){
                $featureExt = ($featureExt)
                    ->setNbParking($item->parking->nbParking)
                    ->setNbBox($item->parking->nbBox)
                ;
            }
            if($item->plus != null){
                $featureExt = ($featureExt)
                    ->setHasElevator($item->plus->hasElevator)
                    ->setHasCellar($item->plus->hasCella)
                    ->setHasIntercom($item->plus->hasIntercom)
                    ->setHasConcierge($item->plus->hasConcierge)
                    ->setHasTerrace($item->plus->hasTerrace)
                    ->setHasClim($item->plus->hasClim)
                    ->setHasPiscine($item->plus->hasPiscine)
                ;
            }
        }else{
            return null;
        }

        return $featureExt;
    }

    private function createDiagnosticFromJson(?ImDiagnostic $diagnostic, $item): ?ImDiagnostic
    {
        if($item){
            $diagnostic = $diagnostic ?? new ImDiagnostic();
            $diagnostic = ($diagnostic)
                ->setDpeVal($item->dpeVal)
                ->setDpeLettre($item->dpeLettre)
                ->setGesVal($item->gesVal)
                ->setGesLettre($item->gesLettre)
                ->setDateRelease($item->dateRelease ? $this->sanitizeData->createDateFromString($item->dateRelease) : null)
                ->setVersionDpe($item->versionDpe)
                ->setDpeMinConso($item->dpeMinConso)
                ->setDpeMaxConso($item->dpeMaxConso)
                ->setDpeRefConso($item->dpeRefConso)
            ;
        }else{
            return null;
        }

        return $diagnostic;
    }

    private function createCoproFromJson(?ImCopro $copro, $item): ?ImCopro
    {
        if($item){
            $copro = $copro ?? new ImCopro();
            $copro = ($copro)
                ->setNbLot($item->nbLot)
                ->setChargesAnnuelle($item->chargesAnnuelle)
                ->setHasProced($item->hasProced)
                ->setDetailsProced($item->detailsProced)
            ;
        }else{
            return null;
        }

        return $copro;
    }

    private function createResponsableFromJson(?ImResponsable $responsable, $item): ?ImResponsable
    {
        if($item){
            $responsable = $responsable ?? new ImResponsable();
            $responsable = ($responsable)
                ->setName($item->name)
                ->setPhone($item->phone)
                ->setEmail($item->email)
                ->setCodeNego($item->codeNego)
            ;
        }else{
            return null;
        }

        return $responsable;
    }

    private function deleteImages($images)
    {
        foreach ($images as $img){
            $this->em->remove($img);
        }

        $this->em->flush();
    }
}