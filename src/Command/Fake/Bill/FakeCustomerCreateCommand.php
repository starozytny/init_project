<?php

namespace App\Command\Fake\Bill;

use App\Entity\Bill\BiContractCustomer;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiSite;
use App\Service\Data\Bill\DataBill;
use App\Service\DatabaseService;
use Exception;
use Faker\Factory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeCustomerCreateCommand extends Command
{
    protected static $defaultName = 'fake:customer:create';
    protected static $defaultDescription = 'Create fake customers';
    private $em;
    private $registry;
    private $databaseService;
    private $dataEntity;

    public function __construct(ManagerRegistry $registry, DatabaseService $databaseService, DataBill $dataEntity)
    {
        parent::__construct();

        $this->em = $registry->getManager();
        $this->registry = $registry;
        $this->databaseService = $databaseService;
        $this->dataEntity = $dataEntity;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Reset des tables');
        $biSocieties = $this->databaseService->resetTableAndGetSocieties($io, [BiContractCustomer::class, BiSite::class, BiCustomer::class]);

        $fake = Factory::create();
        $io->title('Création de 2600 clients fake');
        for($i=0; $i<2600 ; $i++) {

            $data = [
                'name' => $fake->name,
                'address' => $fake->streetName,
                'address2' => $fake->numberBetween(0, 1) ? $fake->streetName : null,
                'complement' => $fake->numberBetween(0, 1) ? $fake->streetName : null,
                'zipcode' => $fake->postcode,
                'city' => $fake->city,
                'country' => $fake->country,
                'email' => $fake->email,
                'phone' => $fake->e164PhoneNumber,
                'numeroTva' => $fake->numberBetween(0,1) ? $fake->e164PhoneNumber : null
            ];

            $data = json_decode(json_encode($data));

            $soc = $biSocieties[$fake->numberBetween(0, count($biSocieties) - 1)];
            $society = $soc['society'];

            $new = $this->dataEntity->setDataCustomer(new BiCustomer(), $data, $society);
            $new = ($new)
                ->setNumero($this->dataEntity->createNumero("customer", new \DateTime(), $society))
            ;

            $this->em->persist($new);
        }
        $this->em->flush();

        $io->text('CUSTOMERS : Clients fake créés' );
        foreach($biSocieties as $s){
            $society = $s['society'];

            $customers = $this->em->getRepository(BiCustomer::class)->findBy(['society' => $society]);

            $io->title('Création de 1300 sites fake');
            for($i=0; $i<1300 ; $i++) {

                $customer = $customers[$fake->numberBetween(0, count($customers) - 1)];

                $data = [
                    'numero' => $fake->randomDigitNotNull . uniqid(),
                    'name' => $fake->name,
                    'address' => $fake->streetName,
                    'address2' => $fake->numberBetween(0, 1) ? $fake->streetName : null,
                    'complement' => $fake->numberBetween(0, 1) ? $fake->streetName : null,
                    'zipcode' => $fake->postcode,
                    'city' => $fake->city,
                    'country' => $fake->country
                ];

                $data = json_decode(json_encode($data));

                $new = $this->dataEntity->setDataSite(new BiSite(), $data, $customer, $society);
                $this->em->persist($new);
            }
            $this->em->flush();
            $io->text('SITES : Sites fake créés' );
        }

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return Command::SUCCESS;
    }
}
