<?php


namespace App\Manager;


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
        $bien = new ImBien(); $existe = false;
        foreach($biens as $b){
            if($b->getRef() == $data->bien->ref && $agency->getId() == $b->getAgency()->getId()){
                $bien = $b;
            }
        }

        $item = $data->bien;

        $bien = ($bien)
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
            ->setIsSync(true)
        ;

        $this->em->persist($bien);

       return $bien;
    }
}