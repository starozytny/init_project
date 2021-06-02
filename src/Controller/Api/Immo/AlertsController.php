<?php

namespace App\Controller\Api\Immo;

use App\Entity\Contact;
use App\Entity\Immo\ImAlert;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\Immo\ImAlertRepository;
use App\Service\ApiResponse;
use App\Service\MailerService;
use App\Service\SanitizeData;
use App\Service\SettingsService;
use App\Service\ValidatorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/immo/alerts", name="api_alerts_")
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
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        if (!isset($data->email)) {
            return $apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        $email = trim($data->email);

        $existe = $em->getRepository(ImAlert::class)->findOneBy(['email' => $email, 'typeAd' => $data->typeAd, 'typeBien' => $data->typeBien]);
        if($existe){
            return $apiResponse->apiJsonResponseBadRequest('Vous avez déjà créer une alerte pour cette recherche.');
        }

        $obj = (new ImAlert())
            ->setEmail($email)
            ->setTypeAd($data->typeAd)
            ->setTypeBien($data->typeBien)
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Alerte enregistrée.");
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
}
