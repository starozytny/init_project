<?php

namespace App\Controller;

use App\Entity\Formation\FoRegistration;
use App\Entity\Formation\FoSession;
use App\Entity\Formation\FoWorker;
use App\Entity\Paiement\PaBank;
use App\Entity\Paiement\PaOrder;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function profil(Request $request, SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();
        /** @var User $obj */
        $obj = $this->getUser();
        $teams = $em->getRepository(FoWorker::class)->findBy(['user' => $obj, 'isArchived' => false]);
        $teamsArchived = $em->getRepository(FoWorker::class)->findBy(['user' => $obj, 'isArchived' => true]);

        $teams = $serializer->serialize($teams, 'json', ['groups' => User::USER_READ]);
        $teamsArchived = $serializer->serialize($teamsArchived, 'json', ['groups' => User::USER_READ]);

        $banks = $obj->getPaBanks();
        $orders = $obj->getPaOrders();

        $banks = $serializer->serialize($banks, 'json', ['groups' => User::USER_READ]);
        $orders = $serializer->serialize($orders, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('user/pages/profil/index.html.twig',  [
            'obj' => $obj,
            'banks' => $banks,
            'orders' => $orders,
            'teams' => $teams,
            'teamsArchived' => $teamsArchived,
            '_error' => $request->query->get('_error')
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
    public function teamUpdate(FoWorker $obj, SerializerInterface $serializer): Response
    {
        if(count($obj->getRegistrations()) != 0){
            return $this->redirectToRoute("user_profil", ['_error' => 1]);
        }

        $data = $serializer->serialize($obj, 'json', ['groups' => User::USER_READ]);
        return $this->render('user/pages/profil/team/update.html.twig', ['elem' => $obj, 'donnees' => $data]);
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
    public function sessions(SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository(FoSession::class)->findBy(['isPublished' => true], ['start' => 'ASC']);

        $objs = $serializer->serialize($objs, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('user/pages/sessions/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/formation/sessions/{slug}", options={"expose"=true}, name="registration")
     */
    public function registration(FoSession $obj, SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();

        /** @var User $user */
        $user = $this->getUser();
        $workersRegistered = [];

        $orders = $em->getRepository(PaOrder::class)->findBy(['user' => $user, 'session' => $obj]);
        if(count($orders) > 0){
            $ordersId = [];
            foreach($orders as $order){
                if($order->getStatus() == PaOrder::STATUS_ATTENTE){
                    return $this->render('user/pages/sessions/registration/index.html.twig', ['elem' => $obj, 'error' => true]);
                }elseif($order->getStatus() == PaOrder::STATUS_VALIDER || $order->getStatus() == PaOrder::STATUS_TRAITER){
                    $ordersId[] = $order->getId();
                }
            }

            $registrations = $em->getRepository(FoRegistration::class)->findBy(['paOrder' => $ordersId]);
            foreach($registrations as $registration){
                $workersRegistered[] = $registration->getWorker();
            }
        }

        $workers = $em->getRepository(FoWorker::class)->findBy(['user' => $user, 'isArchived' => false]);
        $banks   = $em->getRepository(PaBank::class)->findBy(['user' => $user]);

        $session            = $serializer->serialize($obj, 'json', ['groups' => User::ADMIN_READ]);
        $banks              = $serializer->serialize($banks, 'json', ['groups' => User::USER_READ]);
        $workers            = $serializer->serialize($workers, 'json', ['groups' => User::USER_READ]);
        $workersRegistered  = $serializer->serialize($workersRegistered, 'json', ['groups' => User::USER_READ]);

        return $this->render('user/pages/sessions/registration/index.html.twig', [
            'elem' => $obj,
            'session' => $session,
            'banks' => $banks,
            'workers' => $workers,
            'workersRegistered' => $workersRegistered
        ]);
    }

    /**
     * @Route("/formation/sessions/{slug}/modification", options={"expose"=true}, name="registration_update")
     */
    public function registrationUpdate(FoSession $obj, SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $data = [];
        $registrations = $em->getRepository(FoRegistration::class)->findBy(['user' => $user, 'session' => $obj, "status" => FoRegistration::STATUS_ACTIVE]);
        if(count($registrations) > 0){
            foreach($registrations as $registration){
                if($registration->getPaOrder()->getStatus() == PaOrder::STATUS_ATTENTE){
                    return $this->render('user/pages/sessions/registration/update.html.twig', ['elem' => $obj, 'error' => true]);
                }elseif($registration->getPaOrder()->getStatus() == PaOrder::STATUS_VALIDER){
                    $data[] = $registration;
                }
            }
        }

        $workers = $em->getRepository(FoWorker::class)->findBy(['user' => $user, 'isArchived' => false]);

        $workers       = $serializer->serialize($workers, 'json', ['groups' => User::USER_READ]);
        $registrations = $serializer->serialize($data, 'json', ['groups' => User::USER_READ]);

        return $this->render('user/pages/sessions/registration/update.html.twig', [
            'elem' => $obj,
            'workers' => $workers,
            'registrations' => $registrations
        ]);
    }

    /**
     * @Route("/mes-formations", options={"expose"=true}, name="my_formations")
     */
    public function myFormations(SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository(FoRegistration::class)->findBy(['user' => $this->getUser(), 'status' => FoRegistration::STATUS_ACTIVE]);

        $sessions = []; $noDuplication = [];
        foreach($objs as $obj){
            $sessionId = $obj->getSession()->getId();
            if(!in_array($sessionId, $noDuplication)){
                $noDuplication[] = $sessionId;
                $sessions[] = $obj->getSession();
            }
        }

        $sessions = $serializer->serialize($sessions, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('user/pages/sessions/own.html.twig',  [
            'donnees' => $sessions,
        ]);
    }
}
