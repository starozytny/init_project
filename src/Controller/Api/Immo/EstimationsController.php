<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImEstimation;
use App\Entity\User;
use App\Repository\Immo\ImEstimationRepository;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
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
 * @Route("/api/immo/estimations", name="api_immo_estimations_")
 */
class EstimationsController extends AbstractController
{
    /**
     * Admin - Get array of estimations
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="index", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns array of estimations",
     * )
     * @OA\Tag(name="Estimations")
     *
     * @param ImEstimationRepository $repository
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function index(ImEstimationRepository $repository, ApiResponse $apiResponse): JsonResponse
    {
        $objs = $repository->findAll();
        return $apiResponse->apiJsonResponse($objs, User::ADMIN_READ);
    }

    /**
     * Create an estimation
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an estimation",
     * )
     *
     * @OA\Tag(name="Estimations")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param SanitizeData $sanitizeData
     * @param ApiResponse $apiResponse
     * @param MailerService $mailerService
     * @param SettingsService $settingsService
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, SanitizeData $sanitizeData, ApiResponse $apiResponse,
                           MailerService $mailerService, SettingsService $settingsService): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        if (!isset($data->email) || !isset($data->typeAd) || !isset($data->typeBien)) {
            return $apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        $email = trim($data->email);
        $typeAd = $data->typeAd === "0" ? "Location" : "Vente";
        $typeBiens = ["Maison", "Appartement", "Parking", "Bureau", "Local", "Immeuble", "Terrain", "Commerce"];
        $etats = ["Neuf", "Rénové", "Moyen", "Bon", "Travaux à prévoir"];
        $ext = $data->ext;
        sort($ext);
        $ext = implode(",", $ext);

        $obj = (new ImEstimation())
            ->setZipcode($data->zipcode)
            ->setCity($data->city)
            ->setLastname($sanitizeData->sanitizeString($data->lastname))
            ->setFirstname($sanitizeData->sanitizeString($data->firstname))
            ->setPhone($data->phone)
            ->setEmail($email)
            ->setTypeAd($typeAd)
            ->setTypeBien($typeBiens[$data->typeBien])
            ->setConstructionYear($sanitizeData->sanitizeString($data->constructionYear))
            ->setEtat($etats[$data->etat])
            ->setArea($data->area)
            ->setAreaLand($sanitizeData->sanitizeString($data->areaLand))
            ->setNbPiece($data->nbPiece)
            ->setNbRoom($sanitizeData->sanitizeString($data->nbRoom))
            ->setNbParking($data->nbParking)
            ->setInfos($sanitizeData->sanitizeString($data->infos))
            ->setExt($ext)
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        if($mailerService->sendMail(
                $settingsService->getEmailContact(),
                "[" . $settingsService->getWebsiteName() ."] Demande d'estimation",
                "Demande d'estimation réalisé à partir de " . $settingsService->getWebsiteName(),
                'app/email/immo/estimation.html.twig',
                ['contact' => $obj, 'settings' => $settingsService->getSettings()]
            ) != true)
        {
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "La demande n\'a pas pu être délivré. Veuillez revenir ultérieurement."
            ]]);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Delete an estimation
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Estimations")
     *
     * @param DataService $dataService
     * @param ImEstimation $obj
     * @return JsonResponse
     */
    public function delete(DataService $dataService, ImEstimation $obj): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Admin - Delete a group of estimation
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
     * @OA\Tag(name="Estimations")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteGroup(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(ImEstimation::class, json_decode($request->getContent()));
    }
}