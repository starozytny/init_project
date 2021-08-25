<?php


namespace App\Service\Immo;


use App\Entity\Immo\ImAgency;
use App\Entity\Immo\ImBien;
use Doctrine\ORM\EntityManagerInterface;

class ImmoService
{
    private $em;
    private $imagesDirectory;
    private $thumbsDirectory;

    public function __construct($imagesDirectory, $thumbsDirectory, EntityManagerInterface $entityManager)
    {

        $this->em = $entityManager;
        $this->imagesDirectory = $imagesDirectory;
        $this->thumbsDirectory = $thumbsDirectory;
    }

    public function getImagesDirectory()
    {
        return $this->imagesDirectory;
    }

    public function getThumbsDirectory()
    {
        return $this->thumbsDirectory;
    }

    /**
     * @param $param = locations / ventes
     * @return array
     */
    public function getTypeAdFormParamString($param): array
    {
        if($param === 'locations'){
            return [ImBien::NATURE_LOCATION, ImBien::NATURE_VACANCE];
        }

        return [ImBien::NATURE_VENTE, ImBien::NATURE_VIAGER,
            ImBien::NATURE_INVEST, ImBien::NATURE_PRESTIGE,
            ImBien::NATURE_BAIL, ImBien::NATURE_COMMERCE];
    }

    /**
     * @param ImAgency $agency
     */
    public function deleteAgency(ImAgency $agency)
    {
        // remove bien of demandes
        if(count($agency->getBiens()) !== 0){
            foreach($agency->getBiens() as $bien){
                foreach($bien->getDemandes() as $demande){
                    $demande->setBien(null);
                }

                // remove pictures
                foreach($bien->getImages() as $image){
                    $img = $this->getImagesDirectory() . $agency->getDirname() . '/' . $image->getFile();
                    $thumb = $this->getThumbsDirectory() . $agency->getDirname() . '/' . $image->getThumb();
                    if(file_exists($img)){
                        unlink($img);
                    }
                    if(file_exists($thumb)){
                        unlink($thumb);
                    }
                }

                //remove bien
                $this->em->remove($bien);
            }
        }

        //remove stats
        if(count($agency->getStats()) !== 0){
            foreach($agency->getStats() as $stat){
                $this->em->remove($stat);
            }
        }

        //remove agency
        $this->em->remove($agency);
    }
}