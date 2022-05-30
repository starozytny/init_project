<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Bill\BiTaxe;
use App\Entity\Bill\BiUnity;
use App\Service\Bill\BillService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/facturation/parametres", name="admin_bill_settings_")
 */
class SettingsController extends AbstractController
{
    private $doctrine;
    private $billService;

    public function __construct(ManagerRegistry $doctrine, BillService $billService)
    {
        $this->doctrine = $doctrine;
        $this->billService = $billService;
    }

    /**
     * @Route("/entreprise", name="index")
     */
    public function settings(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $society = $this->billService->getSociety($user);
        $society = $serializer->serialize($society, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('admin/pages/bill/settings/index.html.twig',  [
            'pageName' => "entreprise",
            'society' => $society,
        ]);
    }

    /**
     * @Route("/taxes", name="taxes_index")
     */
    public function taxes(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();

        $society = $this->billService->getSociety($user);
        $objs = $em->getRepository(BiTaxe::class)->findBy(['society' => [null, $society]]);

        $objs = $serializer->serialize($objs, 'json', ['groups' => User::USER_READ]);

        return $this->render('admin/pages/bill/settings/taxe.html.twig', [
            'pageName' => "taxes",
            'donnees' => $objs,
            'society' => $society,
        ]);
    }

    /**
     * @Route("/unites", name="unities_index")
     */
    public function unities(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();

        $society = $this->billService->getSociety($user);
        $objs = $em->getRepository(BiUnity::class)->findBy(['society' => [null, $society]]);

        $objs = $serializer->serialize($objs, 'json', ['groups' => User::USER_READ]);

        return $this->render('admin/pages/bill/settings/unity.html.twig', [
            'pageName' => "unites",
            'donnees' => $objs,
            'society' => $society,
        ]);
    }

    /**
     * @Route("/notes", name="notes")
     */
    public function notes(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $society = $this->billService->getSociety($user);
        $society = $serializer->serialize($society, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('admin/pages/bill/settings/notes.html.twig',  [
            'pageName' => "notes",
            'society' => $society,
        ]);
    }

    /**
     * @Route("/comptabilité", name="compta_index")
     */
    public function compta(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $society = $this->billService->getSociety($user);

        return $this->render('admin/pages/bill/settings/compta.html.twig',  [
            'pageName' => "comptabilite",
            'society' => $society,
        ]);
    }
}
