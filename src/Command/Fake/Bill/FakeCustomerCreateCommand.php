<?php

namespace App\Command\Fake\Bill;

use App\Entity\Bill\BiCustomer;
use App\Entity\Society;
use App\Service\Data\Bill\DataInvoice;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeCustomerCreateCommand extends Command
{
    protected static $defaultName = 'fake:customer:create';
    protected static $defaultDescription = 'Create fake customers';
    private $em;
    private $databaseService;
    private $dataEntity;

    public function __construct(EntityManagerInterface $entityManager, DatabaseService $databaseService, DataInvoice $dataEntity)
    {
        parent::__construct();

        $this->em = $entityManager;
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
        $this->databaseService->resetTable($io, [BiCustomer::class]);

        $society = $this->em->getRepository(Society::class)->findOneBy(['name' => 'Logilink']);

        $io->title('Création de 60 clients fake');
        $fake = Factory::create();
        for($i=0; $i<60 ; $i++) {

            $toName = $fake->name;

            $data = [
                'name' => $toName,
                'address' => $fake->streetName,
                'complement' => null,
                'zipcode' => $fake->streetName,
                'toZipcode' => $fake->postcode,
                'city' => $fake->city,
                'country' => $fake->country,
                'email' => $fake->email,
                'phone' => $fake->e164PhoneNumber,
                'numeroTva' => $fake->numberBetween(0,1) ? $fake->e164PhoneNumber : null
            ];

            $data = json_decode(json_encode($data));

            $new = $this->dataEntity->setDataCustomer(new BiCustomer(), $data, $society);
            $this->em->persist($new);
        }

        $io->text('CUSTOMERS : Clients fake créés' );

        $this->em->flush();

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return Command::SUCCESS;
    }
}
