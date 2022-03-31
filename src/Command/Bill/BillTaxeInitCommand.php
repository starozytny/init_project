<?php

namespace App\Command\Bill;

use App\Entity\Bill\BiTaxe;
use App\Service\Data\Bill\DataTaxe;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BillTaxeInitCommand extends Command
{
    protected static $defaultName = 'bill:taxe:init';
    protected static $defaultDescription = 'Initiate taxes';
    private $em;
    private $databaseService;
    private $dataEntity;

    public function __construct(EntityManagerInterface $entityManager, DatabaseService $databaseService, DataTaxe $dataEntity)
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
//        $this->databaseService->resetTable($io, [BiTaxe::class]);

        $existe = $this->em->getRepository(BiTaxe::class)->findAll();
        if(count($existe) != 0){
            $io->text('Taxes déjà initialisées.');
        }

        $values = [0, 20, 13, 10, 8.5, 5.5, 2.1];

        $io->title('Création des taxes');
        $i = 0;
        foreach ($values as $item) {

            $data = [
                "code" => $i,
                "rate" => $item
            ];

            $data = json_decode(json_encode($data));

            $new = $this->dataEntity->setData(new BiTaxe(), $data);
            $new->setIsNatif(true);

            $this->em->persist($new);
            $i++;
        }

        $this->em->flush();

        $io->text('TAXES : Taxes créées');

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }
}
