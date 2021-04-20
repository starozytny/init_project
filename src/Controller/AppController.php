<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(): Response
    {
        return $this->render('app/pages/index.html.twig');
    }

    /**
     * @Route("/legales/mentions-legales", name="app_mentions")
     */
    public function mentions(): Response
    {
        return $this->render('app/pages/legales/mentions.html.twig');
    }

    /**
     * @Route("/legales/politique-confidentialite", name="app_politique")
     */
    public function politique(): Response
    {
        return $this->render('app/pages/legales/politique.html.twig');
    }

    /**
     * @Route("/legales/cookies", name="app_cookies")
     */
    public function cookies(): Response
    {
        return $this->render('app/pages/legales/cookies.html.twig');
    }

    /**
     * @Route("/legales/rgpd", name="app_rgpd")
     */
    public function rgpd(): Response
    {
        return $this->render('app/pages/legales/rgpd.html.twig');
    }

    /**
     * @Route("/syndic", name="app_syndic")
     */
    public function syndic(): Response
    {
        return $this->render('app/pages/services/syndic.html.twig');
    }

    /**
     * @Route("/gestion-locative", name="app_gestion_locative")
     */
    public function gestionLocative(): Response
    {
        return $this->render('app/pages/services/gestion.html.twig');
    }

    /**
     * @Route("/annonces-immobilieres/{ad}", name="app_ads")
     */
    public function ads($ad): Response
    {
        return $this->render('app/pages/ads/index.html.twig');
    }
}
