<?php

namespace App\Controller;

use App\Entity\Bill\BiInvoice;
use App\Entity\Changelog;
use App\Entity\User;
use App\Service\Bill\BillService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Mpdf\MpdfException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

    /**
     * @Route("/", options={"expose"=true}, name="homepage")
     */
    public function index(): Response
    {
        $em = $this->doctrine->getManager();

        $changelogs = $em->getRepository(Changelog::class)->findBy(['isPublished' => true], ['createdAt' => 'DESC'], 5);

        return $this->render('user/pages/index.html.twig', [
            'changelogs' => $changelogs
        ]);
    }

    /**
     * @Route("/profil", options={"expose"=true}, name="profil")
     */
    public function profil(SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();
        /** @var User $obj */
        $obj = $this->getUser();
        $users = $em->getRepository(User::class)->findBy(['society' => $obj->getSociety()]);

        $users = $serializer->serialize($users, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('user/pages/profil/index.html.twig',  [
            'obj' => $obj,
            'users' => $users
        ]);
    }

    /**
     * @Route("/modifier-profil", name="profil_update")
     */
    public function profilUpdate(SerializerInterface $serializer): Response
    {
        /** @var User $obj */
        $obj = $this->getUser();
        $data = $serializer->serialize($obj, 'json', ['groups' => User::ADMIN_READ]);
        return $this->render('user/pages/profil/update.html.twig',  ['elem' => $obj, 'donnees' => $data]);
    }

    /**
     * @Route("/utilisateur/ajouter", options={"expose"=true}, name="user_create")
     *
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function userCreate(): Response
    {
        /** @var User $obj */
        $obj = $this->getUser();
        return $this->render('user/pages/profil/user/create.html.twig', ['elem' => $obj]);
    }

    /**
     * @Route("/utilisateur/modifier/{username}", options={"expose"=true}, name="user_update")
     *
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function userUpdate(User $obj, SerializerInterface $serializer): Response
    {
        $data = $serializer->serialize($obj, 'json', ['groups' => User::ADMIN_READ]);
        return $this->render('user/pages/profil/user/update.html.twig', ['elem' => $obj, 'donnees' => $data]);
    }

    /**
     * @Route("/facture/{id}", name="invoice")
     * @throws MpdfException
     */
    public function invoice(BiInvoice $obj, BillService $billService): Response
    {
        $em = $this->doctrine->getManager();

        if(!$this->isGranted('ROLE_ADMIN')){
            $obj->setIsSeen(true);
        }

        $em->flush();
        return $billService->getInvoice([$obj]);
    }
}
