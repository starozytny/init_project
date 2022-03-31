<?php

namespace App\Controller;

use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiItem;
use App\Entity\Changelog;
use App\Entity\Contact;
use App\Entity\Notification;
use App\Entity\Settings;
use App\Entity\Society;
use App\Entity\User;
use App\Service\Bill\BillService;
use Doctrine\Common\Persistence\ManagerRegistry;
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
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    private function getAllData($classe, SerializerInterface $serializer, $groups = User::ADMIN_READ): string
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository($classe)->findAll();

        return $serializer->serialize($objs, 'json', ['groups' => $groups]);
    }

    private function getRenderView(Request $request, SerializerInterface $serializer, $class, $route): Response
    {
        $objs = $this->getAllData($class, $serializer);
        $search = $request->query->get('search');
        if($search){
            return $this->render($route, [
                'donnees' => $objs,
                'search' => $search
            ]);
        }

        return $this->render($route, [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/", options={"expose"=true}, name="homepage")
     */
    public function index(): Response
    {
        $em = $this->doctrine->getManager();
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
    public function users(Request $request, SerializerInterface $serializer): Response
    {
        $route = 'admin/pages/user/index.html.twig';
        $objs = $this->getAllData(User::class, $serializer);
        $societies = $this->getAllData(Society::class, $serializer);

        $search = $request->query->get('search');
        if($search){
            return $this->render($route, [
                'donnees' => $objs,
                'search' => $search,
                'societies' => $societies
            ]);
        }

        return $this->render($route, [
            'donnees' => $objs,
            'societies' => $societies
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
    public function contact(Request $request, SerializerInterface $serializer): Response
    {
        return $this->getRenderView($request, $serializer, Contact::class, 'admin/pages/contact/index.html.twig');
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
     * @Route("/changelogs", options={"expose"=true}, name="changelogs_index")
     */
    public function changelogs(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(Changelog::class, $serializer, User::USER_READ);

        return $this->render('admin/pages/changelog/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    /**
     * @Route("/societes", name="societies_index")
     */
    public function societies(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(Society::class, $serializer);
        $users= $this->getAllData(User::class, $serializer, Society::COUNT_READ);

        return $this->render('admin/pages/society/index.html.twig', [
            'donnees' => $objs,
            'users' => $users
        ]);
    }

    /**
     * @Route("/facturations/factures", name="bill_invoice_index")
     */
    public function invoice(SerializerInterface $serializer): Response
    {
        $em = $this->doctrine->getManager();

        /** @var User $user */
        $user = $this->getUser();
        $society = $em->getRepository(Society::class)->find($user->getSociety()->getId());

        $objs = $this->getAllData(BiInvoice::class, $serializer, BiInvoice::INVOICE_READ);
        $society = $serializer->serialize($society, 'json', ['groups' => User::ADMIN_READ]);

        return $this->render('admin/pages/bill/invoice.html.twig', [
            'donnees' => $objs,
            'society' => $society
        ]);
    }

    /**
     * @Route("/facturations/articles", name="bill_items_index")
     */
    public function billItems(SerializerInterface $serializer, BillService $billService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $society = $user->getSociety();
        $objs = $this->getAllData(BiItem::class, $serializer, BiItem::ITEM_READ);

        [$taxes, $unities] = $billService->getTaxesAndUnitiesData($society, true);

        return $this->render('admin/pages/bill/item.html.twig', [
            'donnees' => $objs,
            'society' => $society,
            'taxes' => $taxes,
            'unities' => $unities,
        ]);
    }
}
