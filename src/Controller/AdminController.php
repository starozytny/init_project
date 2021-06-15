<?php

namespace App\Controller;

use App\Entity\Immo\ImBien;
use App\Entity\Settings;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", options={"expose"=true}, name="homepage")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $settings = $em->getRepository(Settings::class)->findAll();

        $totalUsers = count($users); $nbConnected = 0;
        foreach($users as $user){
            if($user->getLastLogin()){
                $nbConnected++;
            }
        }
        return $this->render('admin/pages/index.html.twig', [
            'settings' => $settings ? $settings[0] : null,
            'totalUsers' => $totalUsers,
            'nbConnected' => $nbConnected,
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
    public function users(): Response
    {
        return $this->render('admin/pages/user/index.html.twig');
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
    public function contact(): Response
    {
        return $this->render('admin/pages/contact/index.html.twig');
    }

    /**
     * @Route("/immobilier/annonces", name="immo_ads")
     */
    public function ads(): Response
    {
        return $this->render('admin/pages/immo/index.html.twig');
    }

    /**
     * @Route("/immobilier/alertes", name="immo_alerts")
     */
    public function alerts(): Response
    {
        return $this->render('admin/pages/immo/alerts.html.twig');
    }

    /**
     * @Route("/immobilier/estimations", name="immo_estimations")
     */
    public function estimations(): Response
    {
        return $this->render('admin/pages/immo/estimations.html.twig');
    }

    /**
     * @Route("/immobilier/devis", name="immo_devis")
     */
    public function devis(): Response
    {
        return $this->render('admin/pages/immo/devis.html.twig');
    }

    /**
     * @Route("/immobilier/demandes", name="immo_demandes")
     */
    public function demandes(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $biens = $em->getRepository(ImBien::class)->findAll();

        $bien = null;
        if(count($biens) != 0){
            $bien = $biens[0];
        }

        return $this->render('admin/pages/immo/demandes.html.twig', [
            'bien' => $bien
        ]);
    }
}
