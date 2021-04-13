<?php

namespace App\Command;

use App\Service\ApiConnect;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use ZipArchive;

class ImmoDataCreateCommand extends Command
{
    protected static $defaultName = 'immo:data:create';

    protected $filenameData = 'annonces.csv';
    protected $filenameDataMaj = 'Annonces.csv';

    private $url;
    private $params;
    private $api_immo;
    private $PATH_DEPOT;
    private $PATH_EXTRACT;
    private $PATH_ARCHIVE;

    public function __construct(ParameterBagInterface $params, HttpClientInterface $api_immo, ApiConnect $apiConnect)
    {
        parent::__construct();
        $this->params = $params;

        $path_data = $this->params->get('kernel.project_dir') . '/documents/immo/data/';

        $this->PATH_DEPOT   = $path_data . 'depot/';
        $this->PATH_EXTRACT = $path_data . 'extract/';
        $this->PATH_ARCHIVE = $path_data . 'archive/';

        $this->api_immo = $api_immo;

        $this->url = $apiConnect->getUrlApiImmo();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create data immo with api immo custom')
            ->addArgument('call', InputArgument::REQUIRED, '1 si first call command 0 sinon')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // --------------  PROCESSUS  -----------------------
        $call = $input->getArgument('call');
        $this->process($io, $output, $call);

        return Command::SUCCESS;
    }

    protected function process(SymfonyStyle $io, OutputInterface $output, $call): int
    {
        // --------------  RECHERCHE DES ZIP  -----------------------
        $io->title('Recherche et décompression des zips');
        $archives = scandir($this->PATH_DEPOT);
        $folders = $this->extractZIP($io, $archives); // exit auto if no folder

        if($folders == 0){
            if($call != 1){
                // --------------  ADD STAT  -----------------------
            }

            $io->success('Fin de la commande');
            return Command::SUCCESS;
        }else {

            // -------------- SI CEST LE PREMIER CALL  -----------------------
            if($call == 1){
                // --------------  SAVE OLD DATA  -----------------------
                // --------------  RESET TABLE  -----------------------
            }

            // --------------  START PROCESS FOLDER  -----------------------
            $folder = $folders[0]; // get first folder
            $archives = $this->getOriginalArchives($archives);
            $archive = $archives[0];

            // --------------  Reinitialise les dossiers images du folder + MOVE IMG TO PUBLIC  -----------------------

            // --------------  TRANSFERT DES DATA  -----------------------
            $io->title('Traitement du dossier');
            $this->transfertData($io, $folder);
//            try {
//
//            } catch (Exception $e) {$io->error('Error load CSV file : ' . $e);}

            // --------------  TRANSFERT DES ARCHIVES  -----------------------
//            $io->title('Création des archives');
//            $this->archive($archive);
//            $io->comment('Archives terminées');

            // --------------  SUPPRESSION DES ZIP  -----------------------
//            $io->title('Suppresion du ZIP');
//            if (preg_match('/([^\s]+(\.(?i)(zip))$)/i', $archive, $matches)) {
//                $this->deleteZip($archive);
//                $io->text('Suppression du Zip ' . $archive);
//            }
            // --------------  SUPPRESSION DES EXTRACTS  -----------------------
//            $io->title('Suppresion des dossiers Extracts');
//            $folders = scandir($this->PATH_EXTRACT);
//            foreach ($folders as $item) {
//                if ($item != "." && $item != "..") {
//                    $this->deleteFolder($this->PATH_EXTRACT . $item);
//                    $io->text('Suppression du folder ' . $item);
//                }
//            }

//            $io->success('SUIVANT');
//            $this->process($io, $output, 0);
        }

        return Command::SUCCESS;
    }

    /**
     * Fonction permettant de décompresser les zip dans le dossier extract
     * @param $archives
     * @param SymfonyStyle $io
     * @return array|bool
     */
    protected function extractZIP(SymfonyStyle $io, $archives){
        $isEmpty = true;
        $isOpen = false;
        $folders = array();

        foreach ($archives as $item) {

            $archive = new ZipArchive();

            if(preg_match('/([^\s]+(\.(?i)(zip))$)/i', $item, $matches)){

                $isEmpty = false;

                if($archive->open($this->PATH_DEPOT . $item)){

                    $nameFolder = $this->getDirname($item);

                    if($isOpen == false){
                        $archive->extractTo($this->PATH_EXTRACT . $nameFolder);
                        $archive->close(); unset($archive);
                        $io->comment("Archive " . $nameFolder . " extracted [OK]");

                        array_push($folders, $nameFolder);
                        $isOpen = true;
                    }
                }else{
                    $io->error("Erreur archive");
                    return false;
                }
            }
        }
        if($isEmpty){
            $io->comment("Aucun zip dans le dossier dépot.");
            return false;
        }
        return $folders;
    }

    protected function getDirname($item)
    {
        $nameFolder = strtolower(substr($item,0, (strlen($item)-4)));
        return str_replace(" ", "_", $nameFolder);
    }

    protected function getOriginalArchives($archives): array
    {
        $folders = array();
        foreach ($archives as $item) {
            if(preg_match('/([^\s]+(\.(?i)(zip))$)/i', $item, $matches)){
                array_push($folders, $item);
            }
        }
        return $folders;
    }

    /**
     * Transfert des data d'un folder
     * @param SymfonyStyle $io
     * @param $folder
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function transfertData(SymfonyStyle $io, $folder){
//        $tabPathImg = [
//            'images' => $this->PATH_IMAGES,
//            'thumbs' => $this->PATH_THUMBS
//        ];

        $io->comment('------- Dossier : ' . $folder);

        $file = $this->PATH_EXTRACT . $folder . '/' . $this->filenameData;
        $fileMaj = $this->PATH_EXTRACT . $folder . '/' . $this->filenameDataMaj;

        if (file_exists($file) || file_exists($fileMaj)) {
            $data = [
                'dirname' => $folder,
                'identifiant' => $folder,
                'file' => DataPart::fromPath($file),
            ];

            $formData = new FormDataPart($data);
            $response = $this->api_immo->request("POST", $this->url . "api/immo/ad/csv", [
                'headers'=> $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable()
            ]);

            dump(json_decode($response->getContent()));

        } else { // XML --- PERICLES

        }
    }


}
