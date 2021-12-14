<?php

namespace App\Controller;

use App\Entity\Formation\FoFormation;
use App\Entity\Formation\FoSession;
use App\Entity\Formation\FoWorker;
use App\Entity\Paiement\PaBank;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/espace-membre", name="user_")
 */
class UserController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    private function getAllData($classe, SerializerInterface $serializer, $groups = User::USER_READ): string
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository($classe)->findAll();

        return $serializer->serialize($objs, 'json', ['groups' => $groups]);
    }

    /**
     * @Route("/", options={"expose"=true}, name="homepage")
     */
    public function index(): Response
    {
        return $this->render('user/pages/index.html.twig');
    }

    /**
     * @Route("/profil", options={"expose"=true}, name="profil")
     */
    public function profil(SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();
        /** @var User $obj */
        $obj = $this->getUser();
        $teams = $em->getRepository(FoWorker::class)->findBy(['user' => $obj, 'isArchived' => false]);
        $teams = $serializer->serialize($teams, 'json', ['groups' => User::USER_READ]);

        $banks = $obj->getPaBanks();

        $banks = $serializer->serialize($banks, 'json', ['groups' => User::USER_READ]);

        return $this->render('user/pages/profil/index.html.twig',  [
            'obj' => $obj,
            'teams' => $teams,
            'banks' => $banks
        ]);
    }

    /**
     * @Route("/modifier-profil", name="profil_update")
     */
    public function profilUpdate(SerializerInterface $serializer): Response
    {
        /** @var User $data */
        $data = $this->getUser();
        $data = $serializer->serialize($data, 'json', ['groups' => User::ADMIN_READ]);
        return $this->render('user/pages/profil/update.html.twig',  ['donnees' => $data]);
    }

    /**
     * @Route("/equipe/ajouter", options={"expose"=true}, name="team_create")
     */
    public function teamCreate(): Response
    {
        return $this->render('user/pages/profil/team/create.html.twig');
    }

    /**
     * @Route("/equipe/modifier/{id}", options={"expose"=true}, name="team_update")
     */
    public function teamUpdate(FoWorker $worker, SerializerInterface $serializer): Response
    {
        $data = $serializer->serialize($worker, 'json', ['groups' => User::USER_READ]);
        return $this->render('user/pages/profil/team/update.html.twig', ['elem' => $worker, 'donnees' => $data]);
    }

    /**
     * @Route("/ajouter-banque", options={"expose"=true}, name="bank_create")
     */
    public function bankCreate(): Response
    {
        return $this->render('user/pages/profil/bank/create.html.twig');
    }

    /**
     * @Route("/modifier-banque/{id}", options={"expose"=true}, name="bank_update")
     */
    public function bankUpdate(PaBank $obj, SerializerInterface $serializer): Response
    {
        $obj = $serializer->serialize($obj, 'json', ['groups' => User::USER_READ]);
        return $this->render('user/pages/profil/bank/update.html.twig', ['donnees' => $obj]);
    }

    /**
     * @Route("/formations/sessions", name="sessions")
     */
    public function formations(SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository(FoSession::class)->findBy(['isPublished' => true], ['start' => 'ASC']);

        $objs = $serializer->serialize($objs, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('user/pages/sessions/index.html.twig', [
            'donnees' => $objs
        ]);
    }

}
