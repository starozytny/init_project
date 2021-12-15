<?php

namespace App\Command\Cron;

use App\Entity\Paiement\PaOrder;
use App\Service\Data\DataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminOrderRefreshCommand extends Command
{
    protected static $defaultName = 'admin:order:refresh';
    protected $em;
    private $dataService;

    public function __construct(EntityManagerInterface $entityManager, DataService $dataService)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->dataService = $dataService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Refresh formations, if date is passed = close.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $objs = $this->em->getRepository(PaOrder::class)->findBy(['status' => PaOrder::STATUS_ATTENTE], ['codeAt' => 'ASC']);

        $total = 0;
        $now = $this->dataService->createDate();
        foreach($objs as $obj){
            $interval = date_diff($obj->getCodeAt(), $now);

            if($interval->i > 0 || $interval->h > 2 || $interval->d > 0 || $interval->m > 0 || $interval->y > 0){
               $obj->setStatus(PaOrder::STATUS_EXPIRER);
               $obj->setUpdatedAt($this->dataService->createDate());
               $total++;
            }
        }

        $this->em->flush();

        $s = $total > 1 ? "s" : "";
        $io->text($total . " ordre" . $s . " expiré" . $s);

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return 0;
    }
}
