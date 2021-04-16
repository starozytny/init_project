<?php

namespace App\Command;

use App\Entity\Immo\ImAgency;
use App\Entity\Immo\ImBien;
use App\Manager\CreateAgency;
use App\Manager\CreateBien;
use App\Manager\CreateImage;
use App\Service\ApiConnect;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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

    const ANNONCE_CSV = 0;
    const ANNONCE_XML = 1;
    const ANNONCE_APIMMO = 2;

    private $em;
    private $url;
    private $params;
    private $api_immo;
    private $PATH_DEPOT;
    private $PATH_EXTRACT;
    private $PATH_ARCHIVE;
    private $PATH_IMAGES;
    private $PATH_THUMBS;

    private $synchroAgencies = [];

    private $createAgency;
    private $createBien;
    private $createImage;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params,
                                HttpClientInterface $api_immo, ApiConnect $apiConnect,
                                CreateAgency $createAgency, CreateBien $createBien, CreateImage $createImage)
    {
        parent::__construct();
        $this->em       = $em;
        $this->params   = $params;
        $this->api_immo = $api_immo;

        $path_data = $this->params->get('kernel.project_dir') . '/documents/immo/data/';
        $path_public = $this->params->get('kernel.project_dir') . '/public/';

        $this->PATH_DEPOT   = $path_data . 'depot/';
        $this->PATH_EXTRACT = $path_data . 'extract/';
        $this->PATH_ARCHIVE = $path_data . 'archive/';
        $this->PATH_IMAGES = $path_public . 'annonces/images/';
        $this->PATH_THUMBS = $path_public . 'annonces/thumbs/';

        $this->url = $apiConnect->getUrlApiImmo();
        $this->createAgency = $createAgency;
        $this->createBien = $createBien;
        $this->createImage = $createImage;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create data immo with api immo custom')
            ->addArgument('call', InputArgument::REQUIRED, '1 si first call command 0 sinon')
            ->addArgument('haveApimmo', InputArgument::OPTIONAL, '1 si have Apimmo 0 sinon')
        ;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // --------------  PROCESSUS  -----------------------
        $call = $input->getArgument('call');
        $haveApimmo = $input->getArgument('haveApimmo');
        $this->process($io, $output, $call, $haveApimmo);
        $this->removeAgencyNoSynchronized($io);

        return Command::SUCCESS;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function process(SymfonyStyle $io, OutputInterface $output, $call, $haveApimmo): int
    {
        // --------------  RECHERCHE DES ZIP  -----------------------
        $io->title('Recherche et décompression des zips');
        $archives = scandir($this->PATH_DEPOT);
        $folders = $this->extractZIP($io, $archives); // exit auto if no folder

        if($folders == 0){
            if($call != 1){
                // TODO: -------------- ADD STAT  -----------------------
            }

            $io->success('Fin de la commande');
            return Command::SUCCESS;
        }else {

            // --------------  START WITH APIMMO if have it  -----------------------
            if($call == 1 && $haveApimmo == 1){
                // --------------  TRANSFERT DES DATA  -----------------------
                $io->title('Traitement ' . $_ENV['APIMMO_FOLDER']);
                try {
                    $this->transfertData($io, $output, $_ENV['APIMMO_FOLDER'], $haveApimmo);
                } catch (Exception $e) {$io->error('Error transfert data APIMMO : ' . $e);}
            }else{
                // --------------  START PROCESS FOLDER  -----------------------
                $folder = $folders[0]; // get first folder
                $archives = $this->getOriginalArchives($archives);
                $archive = $archives[0];

                // --------------  Reinitialise les dossiers images du folder + MOVE IMG TO PUBLIC  -----------------------
                $io->comment('Suppression des images de ' . $folder);
                $this->deleteFolder($this->PATH_IMAGES . $folder);
                $this->deleteFolder($this->PATH_THUMBS . $folder);
                $io->title('Transfert des images');
                $this->createImage->transferImages($io, $this->PATH_EXTRACT, $this->PATH_IMAGES, $this->PATH_THUMBS, $folder);

                // --------------  TRANSFERT DES DATA  -----------------------
                $io->title('Traitement du dossier');
                try {
                    $this->transfertData($io, $output, $folder, $haveApimmo);
                } catch (Exception $e) {$io->error('Error transfert data : ' . $e);}

                // --------------  TRANSFERT DES ARCHIVES  -----------------------
                $io->title('Création des archives');
                $this->archive($archive);
                $io->comment('Archives terminées');

                // --------------  SUPPRESSION DES ZIP  -----------------------
                $io->title('Suppresion du ZIP');
                if (preg_match('/([^\s]+(\.(?i)(zip))$)/i', $archive, $matches)) {
                    if(file_exists($this->PATH_DEPOT . $archive)){
                        unlink($this->PATH_DEPOT . $archive);
                    }
                    $io->text('Suppression du Zip ' . $archive);
                }
                // --------------  SUPPRESSION DES EXTRACTS  -----------------------
                $io->title('Suppresion des dossiers extracted');
                $folders = scandir($this->PATH_EXTRACT);
                foreach ($folders as $item) {
                    if ($item != "." && $item != "..") {
                        $this->deleteFolder($this->PATH_EXTRACT . $item);
                    }
                }
                $io->text('Suppression du contenu du dossier ' . $folder);
            }

            $io->success('SUIVANT');
            $this->process($io, $output, 0, 0);
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
     * @param OutputInterface $output
     * @param $folder
     * @param $haveApimmo
     * @return null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    protected function transfertData(SymfonyStyle $io, OutputInterface $output, $folder, $haveApimmo)
    {
        $io->comment('------- Dossier : ' . $folder);

        $agencyToCreate = false;
        $agency = $this->em->getRepository(ImAgency::class)->findOneBy(['dirname' => $folder]);
        if(!$agency){
            $agency = new ImAgency();
            $agencyToCreate = true;
            $dirname = $folder; $identifiant = $folder;
        }else{
            $dirname = $agency->getDirname();
            $identifiant = $agency->getIdentifiant();
        }

        array_push($this->synchroAgencies, $dirname);

        if($haveApimmo == 0){
            $file    = $this->PATH_EXTRACT . $folder . '/' . $this->filenameData;
            $fileMaj = $this->PATH_EXTRACT . $folder . '/' . $this->filenameDataMaj;

            if (file_exists($file) || file_exists($fileMaj)) {

                $file = file_exists($file) ? $file : $fileMaj;

                $data = [
                    'dirname'       => $dirname,
                    'identifiant'   => $identifiant,
                    'file'          => DataPart::fromPath($file),
                ];

                $formData = new FormDataPart($data);
                $response = $this->api_immo->request("POST", $this->url . "api/immo/ad/csv", [
                    'headers'=> $formData->getPreparedHeaders()->toArray(),
                    'body' => $formData->bodyToIterable()
                ]);
            } else { // XML --- PERICLES
                $io->comment('------- [ERROR] Fichier introuvable : ' . $folder);
                return null;
            }
        }else{
            $response = $this->api_immo->request("POST", $this->url . "api/immo/ad/apimmo", [
                'body' => [
                    'dirname'           => $dirname,
                    'identifiant'       => $identifiant,
                    'apimmo_agency'     => $_ENV['APIMMO_AGENCY'],
                    'apimmo_provider'   => $_ENV['APIMMO_PROVIDER'],
                    'apimmo_token'      => $_ENV['APIMMO_TOKEN']
                ]
            ]);
        }

        $this->traitement($io, $output, $folder, $agency, $agencyToCreate, json_decode($response->getContent()));
    }

    /**
     * Lance le traitement de du transfert de data
     * @param SymfonyStyle $io
     * @param $output
     * @param $folder
     * @param $agency
     * @param $agencyToCreate
     * @param $data
     * @throws Exception
     */
    protected function traitement(SymfonyStyle $io, $output, $folder, $agency, $agencyToCreate, $data){
        $count = count($data);
        if ($count != 0) {
            $progressBar = new ProgressBar($output, $count);
            $progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%%  Ψ");
            $progressBar->setOverwrite(true);
            $progressBar->start();

            // get biens and init biens with sync false for delete
            $biens = $this->em->getRepository(ImBien::class)->findAll();
            foreach($biens as $b){
                if($agencyToCreate){
                    $b->setIsSync(true);
                }else{
                    if($b->getAgency()->getId() != $agency->getId()){
                        $b->setIsSync(true);
                    }else{
                        $b->setIsSync(false);
                    }
                }
            }
            $this->em->flush();

            // Insertion des data csv dans la DBB
            foreach ($data as $item) {
                $agency = $this->createAgency->createFromJson($agency, $item->agency);
                if($agencyToCreate){
                    $this->em->persist($agency);
                    $this->em->flush();
                    $agencyToCreate = false;
                }

                $bien = $this->createBien->createFromJson($item, $biens, $agency);

                $this->createImage->createFromJson($item->images, $bien, $this->PATH_IMAGES, $this->PATH_THUMBS, $folder);

                $progressBar->advance();
            }
            $progressBar->finish();
            $io->text('------- Completed !');
            $reader = null;unset($reader);unset($records);

            $this->em->flush();

            //delete all biens with sync false
            /** @var ImBien $b */
            $biens = $this->em->getRepository(ImBien::class)->findBy(['isSync' => false]);
            foreach($biens as $b){
                foreach($b->getDemandes() as $demande){
                    $demande->setBien(null);
                }
                $this->em->remove($b);
            }
            $this->em->flush();

        } else {
            $io->warning("Aucune donnée contenu dans le fichier.");
        }
        $io->newLine(1);
    }

    /**
     * Delete folder
     * @param $folder
     */
    protected function deleteFolder($folder)
    {
        if(is_dir($folder)){
            $clean = scandir($folder);

            foreach ($clean as $entry) {
                if ($entry != "." && $entry != "..") {
                    if(file_exists($folder . '/' . $entry)){
                        unlink($folder . '/' . $entry);
                    }
                }
            }
//            rmdir($folder);
        }
    }

    protected function archive($archive)
    {
        if(preg_match('/([^\s]+(\.(?i)(zip))$)/i', $archive, $matches)){

            $nameFolder = $this->getDirname($archive);
            $fileOri = $this->PATH_DEPOT . $archive;
            $fileOld1 =  $this->PATH_ARCHIVE . $nameFolder . '.zip';
            $fileOld2 =  $this->PATH_ARCHIVE . $nameFolder . '_2.zip';

            if(file_exists($fileOld2)){
                unlink($fileOld2);
                copy($fileOld1, $fileOld2);
                unlink($fileOld1);
            }

            if(file_exists($fileOld1)){
                copy($fileOld1, $fileOld2);
                unlink($fileOld1);
            }
            copy($fileOri, $fileOld1);
        }
    }

    protected function removeAgencyNoSynchronized(SymfonyStyle $io)
    {
        $io->title("Suppression des biens des agences non synchronisées.");
        $agencies = $this->em->getRepository(ImAgency::class)->findAll();
        /** @var ImAgency $agency */
        foreach($agencies as $agency){
            if(!in_array($agency->getDirname(), $this->synchroAgencies)){
                $io->comment($agency->getName());
                foreach($agency->getBiens() as $bien){
                    // remove bien of demandes
                    foreach($bien->getDemandes() as $demande){
                        $demande->setBien(null);
                    }

                    // remove pictures
                    foreach($bien->getImages() as $image){
                        $img = $this->PATH_IMAGES . $agency->getDirname() . '/' . $image->getFile();
                        $thumb = $this->PATH_THUMBS . $agency->getDirname() . '/' . $image->getThumb();
                        if(file_exists($img)){
                            unlink($img);
                        }
                        if(file_exists($thumb)){
                            unlink($thumb);
                        }
                    }
                    $this->em->remove($bien);
                }
            }
        }
        $this->em->flush();
    }
}
