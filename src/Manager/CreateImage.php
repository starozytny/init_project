<?php


namespace App\Manager;


use App\Entity\Immo\ImBien;
use App\Entity\Immo\ImImage;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateImage
{
    private $em;
    const TAILLE_W = 150;
    const TAILLE_H = 150;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createFromJson($items, ImBien $bien, $sourceImages, $sourceThumbs, $folder)
    {
        $sourceImages = $sourceImages . $folder . "/";
        $sourceThumbs = $sourceThumbs . $folder . "/";

        $rank = 0;
        foreach ($items as $item) {
            if($item){

                $file = $sourceImages.$item;
                $filename = $item;
                $filenameThumbs = substr($filename, 0, strripos($filename, '.')) . '-thumbs.jpg';

                // check if i have to download image
                // if yes, download and move image to right images and thumbs folder
                $isUrl = substr($item, 0,4);
                if($isUrl == "http" || $isUrl == "https"){
                    $filename = $this->downloadImgURL($item, $sourceImages, $sourceThumbs);

                    if ($filename != null){
                        $filenameThumbs = $this->createThumb($sourceImages, $sourceThumbs, $filename, self::TAILLE_W, self::TAILLE_H);
                    }
                }

                // create image if existe
                if(file_exists($file)){
                    $isPortrait = true;
                    list($width, $height) = getimagesize($file);
                    if ($width > $height) {
                        $isPortrait = false;
                    }

                    $image = (new ImImage())
                        ->setFile($filename)
                        ->setThumb($filenameThumbs)
                        ->setIsPortrait($isPortrait)
                        ->setRank($rank)
                        ->setBien($bien)
                    ;
                    $rank++;

                    $this->em->persist($image);
                }
            }
        }
    }

    public function transferImages(SymfonyStyle $io, $source, $destinationImages, $destinationThumbs, $folder, $tailleW = self::TAILLE_W, $tailleH = self::TAILLE_H)
    {
        $source = $source . $folder . "/";
        $destinationImages = $destinationImages . $folder . "/";
        $destinationThumbs = $destinationThumbs . $folder . "/";
        $this->createDir($destinationImages);
        $this->createDir($destinationThumbs);

        $config = ['Mode' => 'FULL'];
        $sourceDirectory = scandir($source);

        // ------------------------ CONFIGURATION PHOTO
        $findConfig = false;
        foreach ($sourceDirectory as $file) {
            if (preg_match('/([^\s]+(\.(?i)(cfg))$)/i', $file, $matches)) {
                $config = parse_ini_file($source . '/' . $file);
                $findConfig = true;
            }
        }

        $io->comment('Config Photo : ' . $folder);
        $isEmpty = true;

        if (!$findConfig) {
            $io->text('Fichier config introuvable -> set to default FULL MODE');
        }

        // ------------------------ MOVE PHOTO
        ini_set('gd.jpeg_ignore_warning', true);
        switch ($config['Mode']) {
            case "FULL":
                $io->text("[Photos en mode FULL]");
                foreach ($sourceDirectory as $image) {
                    if (preg_match('/([^\s]+(\.(?i)(JPG|GIF|PNG|JPEG|jpg|gif|png))$)/i', $image, $matches)) {
                        $isEmpty = false;
                        $this->moveImages($io, $source, $destinationImages, $destinationThumbs, $image,  $tailleW, $tailleH);
                    }
                }
                break;
            case "URL":
                $io->text("[Photos en mode URL]");
                $isEmpty = false;
                break;
            default:
                $io->error("Photos en mode DIFF");
                break;
        }

        if ($isEmpty) {
            $io->comment("Dossier " . $folder . " vide ou fichiers non conforme ou config erroné.");
        } else {
            $io->text("--------- Fin transfert.");
        }
        unset($sourceDirectory);

        $io->newLine(1);
    }

    /**
     * Create directory if not exist
     * @param $dir
     */
    protected function createDir($dir){
        if(!is_dir($dir)){
            mkdir($dir);
        }
    }

    /**
     * Function appellant la création de thumb et déplacement des images dans le dossier spécifié
     * @param $io
     * @param $source
     * @param $destinationImages
     * @param $destinationThumbs
     * @param $item
     * @param $tailleW
     * @param $tailleH
     */
    protected function moveImages($io, $source, $destinationImages, $destinationThumbs, $item,  $tailleW, $tailleH)
    {
        $this->createThumb($source, $destinationThumbs, $item, $tailleW, $tailleH);

        // déplacement des images
        if (rename(
            $source . $item,
            $destinationImages . $item
        )) {
        } else {
            $io->warning("L'image : " . $item . " n'a pas été déplacé.");
        }
    }

    /**
     * Resize and create l'image en thumbs
     * @param $source
     * @param $destinationThumbs
     * @param $item
     * @param $tailleW
     * @param $tailleH
     * @return string
     */
    protected function createThumb($source, $destinationThumbs, $item, $tailleW, $tailleH): string
    {
        $file = $source . $item;
        list($width, $height) = getimagesize($file);
        if ($width < $height) { // == portrait
            $tailleH = 200;
        }

        $ratio_orig = $width/$height;
        $w = $tailleW;
        $h = $tailleH;

        if ($w/$h > $ratio_orig) {
            $w = $h*$ratio_orig;
        } else {
            $h = $w/$ratio_orig;
        }

        ini_set('gd.jpeg_ignore_warning', true);
        $src = @imagecreatefromjpeg($file);
        $thumb = imagecreatetruecolor($w, $h);
        @imagecopyresampled($thumb, $src, 0, 0, 0, 0, $w, $h, $width, $height);

        $nameWithoutExt = pathinfo($source .$item)['filename'];
        $name = $nameWithoutExt . '-thumbs.jpg';

        @imagejpeg($thumb, $destinationThumbs . "/" . $name,75);

        return $name;
    }

    /**
     * Download et déplace l'image via une URL
     * @param $file
     * @param $destinationImages
     * @param $destinationThumbs
     * @return bool|string
     */
    public function downloadImgURL($file, $destinationImages, $destinationThumbs)
    {
        try{
            if(!is_dir($destinationImages)){
                mkdir($destinationImages);
            }
            if(!is_dir($destinationThumbs)){
                mkdir($destinationThumbs);
            }
            $current = file_get_contents($file);
            $filename = substr($file, strripos($file, "/")+1 , strlen($file));
            $file = $destinationImages.$filename;
            file_put_contents($file, $current);

            return $filename;
        }catch (Exception $e){
            return  null;
        }
    }

}