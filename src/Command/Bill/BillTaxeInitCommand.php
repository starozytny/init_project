<?php

namespace App\Command\Bill;

use App\Entity\Society;
use App\Entity\Bill\BiTaxe;
use App\Service\Data\Bill\DataTaxe;
use App\Service\DatabaseService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BillTaxeInitCommand extends Command
{
    protected static $defaultName = 'bill:taxe:init';
    protected static $defaultDescription = 'Initiate taxes';
    private $em;
    private $registry;
    private $databaseService;
    private $dataEntity;

    public function __construct(ManagerRegistry $registry, DatabaseService $databaseService, DataTaxe $dataEntity)
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
//            $this->databaseService->resetTable($io, $s->getManager(), [BiTaxe::class]);

            $existe = $em->getRepository(BiTaxe::class)->findAll();
            if(count($existe) != 0){
                $io->text('Taxes déjà initialisées' . $title);
            }else{
                $values = [0, 20, 13, 10, 8.5, 5.5, 2.1];

                $io->title('Création des taxes' . $title);
                $i = 0;
                foreach ($values as $item) {

                    $data = [
                        "code" => $i,
                        "rate" => $item,
                        "numeroComptable" => "44571"
                    ];

                    $data = json_decode(json_encode($data));

                    $new = $this->dataEntity->setData(new BiTaxe(), $data);
                    $new->setIsNatif(true);

                    $em->persist($new);
                    $i++;
                }

                $em->flush();
                $io->text('TAXES : Taxes créées');
            }
        }

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }
}
