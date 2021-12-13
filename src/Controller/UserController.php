<?php

namespace App\Controller;

use App\Entity\Formation\FoWorker;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/espace-membre", name="user_")
 */
class UserController extends AbstractController
{
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
        /** @var User $obj */
        $obj = $this->getUser();
        $data = $serializer->serialize($obj->getFoWorkers(), 'json', ['groups' => User::USER_READ]);

        return $this->render('user/pages/profil/index.html.twig',  [
            'obj' => $obj,
            'donnees' => $data
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
     * @Route("/equipe/ajouter", name="team_create")
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
        return $this->render('user/pages/profil/team/update.html.twig',  ['elem' => $worker, 'donnees' => $data]);
    }
}
