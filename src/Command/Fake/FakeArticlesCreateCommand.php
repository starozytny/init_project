<?php

namespace App\Command\Fake;

use App\Entity\Blog\BoArticle;
use App\Entity\Blog\BoCategory;
use App\Service\DatabaseService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FakeArticlesCreateCommand extends Command
{
    protected static $defaultName = 'fake:articles:create';
    protected static $defaultDescription = 'Create fake articles';
    private $em;
    private $databaseService;

    public function __construct(EntityManagerInterface $entityManager, DatabaseService $databaseService)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->databaseService = $databaseService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Reset des tables');
        $this->databaseService->resetTable($io, [BoArticle::class]);

        $categories = $this->em->getRepository(BoCategory::class)->findAll();
        $nbCategories = count($categories);

        if($nbCategories == 0){
            $io->text("Veuillez créer un ou des catégories avant de lancer cette commande.");
            return Command::FAILURE;
        }

        $io->title('Création de 30 articles fake');
        $fake = Factory::create();
        for($i=0; $i<30 ; $i++) {
            $category = "";
            if($fake->numberBetween(0,1) == 1){
                $n = $categories[$fake->numberBetween(0,$nbCategories - 1)];
                if($n){
                    $category = $n->getId();
                }
            }

            $data = [
                "category" => $category,
            ];

            $data = json_decode(json_encode($data));

            $new = $this->dataImmo->setDataProspect(new BoArticle(), $data);

            $this->em->persist($new);
        }
        $io->text('ARTICLES : Articles fake créés' );

        $this->em->flush();

        $io->newLine();
        $io->comment('--- [FIN DE LA COMMANDE] ---');
        return Command::SUCCESS;
    }
}
