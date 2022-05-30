<?php

namespace App\Command\Bill;

use App\Entity\Society;
use App\Entity\Bill\BiUnity;
use App\Service\Data\Bill\DataUnity;
use App\Service\DatabaseService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BillUnityInitCommand extends Command
{
    protected static $defaultName = 'bill:unity:init';
    protected static $defaultDescription = 'Initiate unities';
    private $em;
    private $registry;
    private $databaseService;
    private $dataEntity;

    public function __construct(ManagerRegistry $registry, DatabaseService $databaseService, DataUnity $dataEntity)
    {
        parent::__construct();

        $this->em = $registry->getManager();
        $this->registry = $registry;
        $this->databaseService = $databaseService;
        $this->dataEntity = $dataEntity;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $societies = $this->em->getRepository(Society::class)->findAll();
        foreach($societies as $s){
            $em = $this->registry->getManager($s->getManager());

            $title = ' pour ' . $s->getName();

//            $io->title('Reset des tables' . $title);
//            $this->databaseService->resetTable($io, $s->getManager(), [BiUnity::class]);

            $existe = $em->getRepository(BiUnity::class)->findAll();
            if(count($existe) != 0){
                $io->text('Unités déjà initialisées' . $title);
            }else{
                $values = ["pièce", "heure", "minute", "jour", "nuit", "semaine", "mois", "année", "kg",
                    "tonne", "litre", "km", "mètre", "m²"];

                $io->title('Création des unités' . $title);
                foreach ($values as $item) {

                    $data = [
                        "name" => $item
                    ];

                    $data = json_decode(json_encode($data));

                    $new = $this->dataEntity->setData(new BiUnity(), $data);
                    $new->setIsNatif(true);

                    $em->persist($new);
                }

                $em->flush();
                $io->text('UNITY : Unités créées');
            }
        }

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }
}
