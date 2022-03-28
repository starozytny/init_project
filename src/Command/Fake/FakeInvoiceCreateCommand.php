<?php

namespace App\Command\Fake;

use App\Entity\Bill\BiInvoice;
use App\Entity\Society;
use App\Service\Bill\BillService;
use App\Service\Data\Bill\DataInvoice;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeInvoiceCreateCommand extends Command
{
    protected static $defaultName = 'fake:invoices:create';
    protected static $defaultDescription = 'Create fake invoices';
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Reset des tables');
        $this->databaseService->resetTable($io, [BiInvoice::class]);
        $society = $this->em->getRepository(Society::class)->findOneBy(['name' => 'Logilink']);
        $society->setCounterInvoice(0);
        $this->em->flush();

        $society = $this->em->getRepository(Society::class)->findOneBy(['name' => 'Logilink']);

        $io->title('Création de 60 facturations fake');
        $fake = Factory::create();
        for($i=0; $i<60 ; $i++) {

            $toName = $fake->name;

            $totalHt = $fake->randomFloat(2);
            $totalRemise = $fake->randomFloat(2);
            $totalTva = ($totalHt - $totalRemise) * (20/100);
            $totalTtc = ($totalHt - $totalRemise) + $totalTva;

            $data = [
                'toName' => $toName,
                'toAddress' => $fake->streetName,
                'toComplement' => $fake->streetName,
                'toZipcode' => $fake->postcode,
                'toCity' => $fake->city,
                'toEmail' => $fake->email,
                'toPhone1' => $fake->e164PhoneNumber,

                'totalHt' => $totalHt,
                'totalRemise' => $totalRemise,
                'totalTva' => $totalTva,
                'totalTtc' => $totalTtc,

                'note' => $fake->sentence,
                'footer' => $fake->sentence,
                'logo' => "data:image/jpeg;base64,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"
            ];

            $data = json_decode(json_encode($data));

            $new = $this->dataEntity->setDataInvoice(new BiInvoice(), $data, $society);

            $dateAt = new \DateTime();

            $new = ($new)
                ->setStatus($fake->numberBetween(0, 6))
                ->setDateAt($dateAt)
                ->setDueAt($dateAt)
                ->setSociety($society)
                ->setNumero($this->dataEntity->createNumero($dateAt, $society))
            ;

            $this->em->persist($new);
        }

        $io->text('INVOICES : Invoices fake créés' );

        $this->em->flush();

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return Command::SUCCESS;
    }
}
