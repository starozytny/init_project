<?php

namespace App\Controller\Api\Immo;

use App\Entity\Contact;
use App\Entity\Immo\ImAlert;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\Immo\ImAlertRepository;
use App\Service\ApiResponse;
use App\Service\Export;
use App\Service\MailerService;
use App\Service\SanitizeData;
use App\Service\SettingsService;
use App\Service\ValidatorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/immo/alerts", name="api_immo_alerts_")
 */
class AlertsController extends AbstractController
{
    /**
     * Admin - Get array of alerts
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="index", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns array of alerts",
     * )
     * @OA\Tag(name="Alerts")
     *
     * @param ImAlertRepository $repository
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function index(ImAlertRepository $repository, ApiResponse $apiResponse): JsonResponse
    {
        $objs = $repository->findAll();
        return $apiResponse->apiJsonResponse($objs, User::ADMIN_READ);
    }

    /**
     * Create a alert
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Alerts")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, MailerService $mailerService,
                           SettingsService $settingsService): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        if (!isset($data->email) || !isset($data->typeAd) || !isset($data->typeBiens)) {
            return $apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        $email = trim($data->email);
        $typeAd = $data->typeAd === "0" ? "Location" : "Vente";
        $typeBiens = $data->typeBiens;
        sort($typeBiens);
        $typeBiens = implode(",", $typeBiens);

        $existe = $em->getRepository(ImAlert::class)->findOneBy(['email' => $email, 'typeAd' => $typeAd, 'typeBiens' => $typeBiens]);
        if($existe){
            return $apiResponse->apiJsonResponseBadRequest('Vous avez déjà créé une alerte pour cette recherche.');
        }

        $obj = (new ImAlert())
            ->setEmail($email)
            ->setTypeAd($typeAd)
            ->setTypeBiens($typeBiens)
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        if($mailerService->sendMail(
                $settingsService->getEmailContact(),
                "[" . $settingsService->getWebsiteName() ."] Ajout d'une alerte",
                "Demande d'alerte réalisé à partir de " . $settingsService->getWebsiteName(),
                'app/email/immo/alerte.html.twig',
                ['contact' => $obj, 'settings' => $settingsService->getSettings()]
            ) != true)
        {
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez revenir ultérieurement."
            ]]);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Delete an alert
     *
     * @Route("/{token}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Alerts")
     *
     * @param ApiResponse $apiResponse
     * @param $token
     * @return JsonResponse
     */
    public function delete(ApiResponse $apiResponse, $token): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $alert = $em->getRepository(ImAlert::class)->findOneBy(['token' => $token]);
        if(!$alert){
            return $apiResponse->apiJsonResponseBadRequest("Cette alerte n'existe pas.");
        }

        $em->remove($alert);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Supression réussie !");
    }

    /**
     * Delete an alert with email
     *
     * @Route("/delete", name="delete", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Alerts")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function deleteWithEmail(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        $alerts = $em->getRepository(ImAlert::class)->findBy(['email' => $data->email]);
        foreach($alerts as $alert){
            $em->remove($alert);
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("Supression réussie !");
    }

    /**
     * Admin - Delete a group of alert
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="delete_group", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Contact")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function deleteGroup(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        $alerts = $em->getRepository(ImAlert::class)->findBy(['id' => $data]);

        if ($alerts) {
            foreach ($alerts as $alert) {
                $em->remove($alert);
            }
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("Supression de la sélection réussie !");
    }

    /**
     * Export list alerts
     *
     * @Route("/export/{format}", name="export", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new user object",
     * )
     *
     * @OA\Tag(name="Alerts")
     *
     * @param Export $export
     * @param $format
     * @return BinaryFileResponse
     */
    public function export(Export $export, $format): BinaryFileResponse
    {
        $em = $this->getDoctrine()->getManager();
        $alerts = $em->getRepository(ImAlert::class)->findAll();
        $data = [];

        foreach ($alerts as $item) {
            $tmp = array(
                $item->getEmail(),
                $item->getTypeAd(),
                $item->getTypeBien(),
                date_format($item->getCreatedAt(), 'd/m/Y'),
            );
            if(!in_array($tmp, $data)){
                array_push($data, $tmp);
            }
        }

        if($format == 'excel'){
            $fileName = 'alerts-email.xlsx';
            $header = array(array('E-mail', 'Nature', 'Type de bien', 'Date de creation'));
        }else{
            $fileName = 'alerts-email.csv';
            $header = array(array('email', 'nature', 'type-bien', 'createAt'));

            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.$fileName.'"');
        }

        $export->createFile($format, 'Liste des alerts email', $fileName , $header, $data, 4, 'export/');
        return new BinaryFileResponse($this->getParameter('private_directory'). 'export/' . $fileName);
    }
}
