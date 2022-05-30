<?php

namespace App\Command\Fake\Bill;

use App\Entity\Society;
use App\Entity\Bill\BiContract;
use App\Entity\Bill\BiContractCustomer;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiHistory;
use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiItem;
use App\Entity\Bill\BiProduct;
use App\Entity\Bill\BiQuotation;
use App\Entity\Bill\BiSite;
use App\Entity\Bill\BiSociety;
use App\Service\Data\Bill\DataBill;
use App\Service\DatabaseService;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeSocietyCreateCommand extends Command
{
    protected static $defaultName = 'fake:society:create';
    protected static $defaultDescription = 'Create fake societies';
    private $em;
    private $registry;
    private $databaseService;
    private $dataBill;

    public function __construct(ManagerRegistry $registry, DatabaseService $databaseService,
                                DataBill $dataBill)
    {
        parent::__construct();

        $this->em = $registry->getManager();
        $this->registry = $registry;
        $this->databaseService = $databaseService;
        $this->dataBill = $dataBill;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Reset des tables');
        $this->databaseService->resetTable($io, [
            BiContractCustomer::class, BiContract::class, BiSite::class, BiCustomer::class, BiHistory::class,
            BiItem::class, BiProduct::class, BiInvoice::class, BiQuotation::class,
            BiSociety::class
        ]);

        $societies = $this->em->getRepository(Society::class)->findAll();

        $fake = Factory::create();

        $io->title('Création de 2 sociétés fake');
        for($i=0; $i<2 ; $i++) {

            $society = $societies[$i];

            $data = [
                "code" => $society->getCode(),
                "name" =>$society->getName(),
                "siren" => "",
                "siret" => "",
                "rcs" => "",
                "numeroTva" => "",
                "forme" => $fake->numberBetween(0, 4),
                "address" => $fake->streetName,
                "address2" => "",
                "zipcode" => $fake->postcode,
                "city" => $fake->city,
                "country" => $fake->country,
                "complement" => $fake->lastName,
                "email" => $fake->email,
                "phone1" => $fake->e164PhoneNumber,
                "bankName" => $fake->company,
                "bankNumero" => $fake->creditCardNumber,
                "bankTitulaire" => $fake->lastName,
                "bankBic" => $fake->swiftBicNumber,
                "bankCode" => $fake->numberBetween(10,50),
                "bankIban" => $fake->iban,
                "noteQuotation" => "",
                "footerQuotation" => "",
                "noteInvoice" => "",
                "footerInvoice" => "",
                "noteAvoir" => "",
                "footerAvoir" => "",

            ];

            $data = json_decode(json_encode($data));

            $new = $this->dataBill->setDataSociety(new BiSociety(), $data);

            $this->em->persist($new);
        }

        $this->em->flush();
        $io->text('SOCIETE : Sociétés fake créées' );

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }
}
