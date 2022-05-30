<?php

namespace App\Command\Fake\Bill;

use App\Entity\Society;
use App\Entity\Bill\BiContract;
use App\Entity\Bill\BiContractCustomer;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiSociety;
use App\Service\Data\Bill\DataBill;
use App\Service\Data\Bill\DataContract;
use App\Service\DatabaseService;
use DateTime;
use Exception;
use Faker\Factory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeContractCreateCommand extends Command
{
    protected static $defaultName = 'fake:contract:create';
    protected static $defaultDescription = 'Create fake contracts';
    private $em;
    private $registry;
    private $databaseService;
    private $dataEntity;
    private $dataBill;

    public function __construct(ManagerRegistry $registry, DatabaseService $databaseService,
                                DataContract $dataEntity, DataBill $dataBill)
    {
        parent::__construct();

        $this->em = $registry->getManager();
        $this->registry = $registry;
        $this->databaseService = $databaseService;
        $this->dataEntity = $dataEntity;
        $this->dataBill = $dataBill;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $biSocieties = [];

        $io->title('Reset des tables');
        $societies = $this->em->getRepository(Society::class)->findAll();
        foreach($societies as $s){
            $this->databaseService->resetTable($io, [BiContractCustomer::class, BiContract::class]);

            $biSociety = $this->em->getRepository(BiSociety::class)->findOneBy(['code' => $s->getCode()]);
            $biSociety->setCounterContract(0);

            $biSocieties[] = [
                'manager' => $s->getManager(),
                'society' => $biSociety
            ];

            $this->em->flush();
        }


        $fake = Factory::create();
        $io->title('Création de 1000 contrats fake');
        for($i=0; $i<1000 ; $i++) {

            $dateAt = new \DateTime();
            $dateAt->setTime(0,0,0);

            $dueAtDay = (int) $dateAt->format("d") + 8;

            $totalHt = $fake->randomFloat(2);
            $totalRemise = $fake->randomFloat(2);
            $totalTva = ($totalHt - $totalRemise) * (20/100);
            $totalTtc = ($totalHt - $totalRemise) + $totalTva;

            $data = [
                'name' => $fake->name,
                'theme' => 1,
                'period' => $fake->numberBetween(1,4),
                'dateAt' => $dateAt->format("Y-m-d\\TH:i:s.000Z"),
                'dueAt' => (new \DateTime())->setDate($dateAt->format("Y"), $dateAt->format("n"), $dueAtDay)->format("Y-m-d\\TH:i:s.000Z"),
                'dueType' => 2,
                'duration' => null,

                'note' => null,
                'footer' => null,

                'totalHt' => $totalHt,
                'totalRemise' => $totalRemise,
                'totalTva' => $totalTva,
                'totalTtc' => $totalTtc,
            ];

            $data = json_decode(json_encode($data));

            $soc = $biSocieties[$fake->numberBetween(0, count($biSocieties) - 1)];
            $society = $soc['society'];

            $new = $this->dataEntity->setDataContract(new BiContract(), $data, $society);
            $new = ($new)
                ->setNumero($this->dataBill->createNumero("contract", new DateTime(), $society))
            ;

            $this->em->persist($new);
        }
        $this->em->flush();

        $io->text('CONTRACTS : Contrats fake créés' );
        foreach($biSocieties as $s) {
            $society = $s['society'];

            $contracts  = $this->em->getRepository(BiContract::class)->findBy(['society' => $society]);
            $customers  = $this->em->getRepository(BiCustomer::class)->findBy(['society' => $society]);

            $io->title('Création de 500 relations contrat-client fake');
            for($i=0; $i<500 ; $i++) {

                $contract = $contracts[$fake->numberBetween(0, count($contracts) - 1)];
                $customer = $customers[$fake->numberBetween(0, count($customers) - 1)];

                $data = [];

                $data = json_decode(json_encode($data));

                $new = $this->dataEntity->setDataRelation(new BiContractCustomer(), $data, $contract, $customer, null);
                $this->em->persist($new);
            }
            $this->em->flush();
            $io->text('CONTRACT-CUSTOMER : Relations fake créés' );
        }


        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return Command::SUCCESS;
    }
}
