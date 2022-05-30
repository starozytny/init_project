<?php


namespace App\Service;


use App\Entity\Society;
use App\Entity\Bill\BiSociety;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function resetTable(SymfonyStyle $io, $list)
    {
        foreach ($list as $item) {
            $objs = $this->em->getRepository($item)->findAll();
            foreach($objs as $obj){
                $this->em->remove($obj);
            }

            $this->em->flush();
        }
        $io->text('Reset [OK]');
    }

    public function resetTableAndGetSocieties(SymfonyStyle $io, array $toDelete): array
    {
        $biSocieties = [];

        $societies = $this->em->getRepository(Society::class)->findAll();
        foreach($societies as $s){
            $this->resetTable($io, $toDelete);

            $biSociety = $this->em->getRepository(BiSociety::class)->findOneBy(['code' => $s->getCode()]);

            $biSocieties[] = [
                'manager' => $s->getManager(),
                'society' => $biSociety
            ];

            $this->em->flush();
        }

        return $biSocieties;
    }
}