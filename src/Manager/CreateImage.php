<?php


namespace App\Manager;


use Symfony\Component\Console\Style\SymfonyStyle;

class CreateImage
{
    public function transferImages(SymfonyStyle $io, $source, $destinationImages, $destinationThumbs, $folder, $tailleW = 150, $tailleH = 150)
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
     */
    protected function createThumb($source, $destinationThumbs, $item, $tailleW, $tailleH)
    {
        $file = $source . $item;
        list($width, $height) = getimagesize($file);

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
    }
}