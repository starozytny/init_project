<?php

namespace App\Controller;

use App\Entity\Paiement\PaBank;
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
        $banks = $obj->getPaBanks();

        $banks = $serializer->serialize($banks, 'json', ['groups' => User::USER_READ]);

        return $this->render('user/pages/profil/index.html.twig',  [
            'obj' => $obj,
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
}
