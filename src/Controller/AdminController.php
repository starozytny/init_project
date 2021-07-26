<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Immo\ImAgency;
use App\Entity\Immo\ImAlert;
use App\Entity\Immo\ImDemande;
use App\Entity\Immo\ImDevis;
use App\Entity\Immo\ImEstimation;
use App\Entity\Immo\ImStat;
use App\Entity\Notification;
use App\Entity\Immo\ImBien;
use App\Entity\Settings;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    private function getAllData($classe, SerializerInterface $serializer, $groups = User::ADMIN_READ): string
    {
        $em = $this->getDoctrine()->getManager();
        $objs = $em->getRepository($classe)->findAll();

        return $serializer->serialize($objs, 'json', ['groups' => $groups]);
    }

    /**
     * @Route("/", options={"expose"=true}, name="homepage")
     */
    public function index(SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $settings = $em->getRepository(Settings::class)->findAll();
        $stats = $em->getRepository(ImStat::class)->findAll();

        $totalUsers = count($users); $nbConnected = 0;
        foreach($users as $user){
            if($user->getLastLogin()){
                $nbConnected++;
            }
        }

        $stats = $serializer->serialize($stats, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('admin/pages/index.html.twig', [
            'settings' => $settings ? $settings[0] : null,
            'totalUsers' => $totalUsers,
            'nbConnected' => $nbConnected,
            'stats' => $stats
        ]);
    }

    /**
     * @Route("/styleguide/html", name="styleguide_html")
     */
    public function styleguideHtml(): Response
    {
        return $this->render('admin/pages/styleguide/index.html.twig');
    }

    /**
     * @Route("/styleguide/react", options={"expose"=true}, name="styleguide_react")
     */
    public function styleguideReact(Request  $request): Response
    {
        if($request->isMethod("POST")){
            return new JsonResponse(['code' => true]);
        }
        return $this->render('admin/pages/styleguide/react.html.twig');
    }

    /**
     * @Route("/utilisateurs", name="users_index")
     */
    public function users(Request $request, SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(User::class, $serializer);
        $search = $request->query->get('search');
        if($search){
            return $this->render('admin/pages/user/index.html.twig', [
                'donnees' => $objs,
                'search' => $search
            ]);
        }

        return $this->render('admin/pages/user/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/parametres", name="settings_index")
     */
    public function settings(): Response
    {
        return $this->render('admin/pages/settings/index.html.twig');
    }

    /**
     * @Route("/contact", name="contact_index")
     */
    public function contact(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(Contact::class, $serializer);

        return $this->render('admin/pages/contact/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/notifications", options={"expose"=true}, name="notifications_index")
     */
    public function notifications(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(Notification::class, $serializer);

        return $this->render('admin/pages/notifications/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/immobilier/annonces", name="immo_ads")
     */
    public function ads(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(ImBien::class, $serializer, ImBien::LIST_READ);

        return $this->render('admin/pages/immo/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/immobilier/agences", name="immo_agencies")
     */
    public function agencies(SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $objs = $this->getAllData(ImAgency::class, $serializer);
        $biens = $em->getRepository(ImBien::class)->findAll();

        return $this->render('admin/pages/immo/agencies.html.twig', [
            'donnees' => $objs,
            'total' => count($biens)
        ]);
    }

    /**
     * @Route("/immobilier/alertes", name="immo_alerts")
     */
    public function alerts(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(ImAlert::class, $serializer);

        return $this->render('admin/pages/immo/alerts.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/immobilier/estimations", name="immo_estimations")
     */
    public function estimations(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(ImEstimation::class, $serializer);

        return $this->render('admin/pages/immo/estimations.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/immobilier/devis", name="immo_devis")
     */
    public function devis(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(ImDevis::class, $serializer);

        return $this->render('admin/pages/immo/devis.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/immobilier/demandes", name="immo_demandes")
     */
    public function demandes(SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $biens = $em->getRepository(ImBien::class)->findAll();

        $bien = null;
        if(count($biens) != 0){
            $bien = $biens[0]; // usefull to test add demande with admin panel
        }

        $objs = $this->getAllData(ImDemande::class, $serializer);

        return $this->render('admin/pages/immo/demandes.html.twig', [
            'bien' => $bien,
            'donnees' => $objs
        ]);
    }
}
