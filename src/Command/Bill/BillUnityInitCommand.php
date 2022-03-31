<?php

namespace App\Command\Bill;

use App\Entity\Bill\BiUnity;
use App\Service\Data\Bill\DataUnity;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BillUnityInitCommand extends Command
{
    protected static $defaultName = 'bill:unity:init';
    protected static $defaultDescription = 'Initiate unities';
    private $em;
    private $databaseService;
    private $dataEntity;

    public function __construct(EntityManagerInterface $entityManager, DatabaseService $databaseService, DataUnity $dataEntity)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->databaseService = $databaseService;
        $this->dataEntity = $dataEntity;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

//        $io->title('Reset des tables');
//        $this->databaseService->resetTable($io, [BiUnity::class]);

        $existe = $this->em->getRepository(BiUnity::class)->findAll();
        if(count($existe) != 0){
            $io->text('Unités déjà initialisées.');
        }

        $values = ["pièce", "heure", "minute", "jour", "nuit", "semaine", "mois", "année", "kg", "tonne", "litre", "km", "mètre", "m²"];

        $io->title('Création des unités');
        foreach ($values as $item) {

            $data = [
                "name" => $item
            ];

            $data = json_decode(json_encode($data));

            $new = $this->dataEntity->setData(new BiUnity(), $data);
            $new->setIsNatif(true);

            $this->em->persist($new);
        }

        $this->em->flush();

        $io->text('UNITY : Unités créées');

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }
}
