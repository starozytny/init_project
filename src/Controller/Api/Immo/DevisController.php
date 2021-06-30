<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImDevis;
use App\Entity\User;
use App\Repository\Immo\ImDevisRepository;
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
 * @Route("/api/immo/devis", name="api_immo_devis_")
 */
class DevisController extends AbstractController
{
    /**
     * Admin - Get array of devis
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="index", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns array of devis",
     * )
     * @OA\Tag(name="Devis")
     *
     * @param ImDevisRepository $repository
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function index(ImDevisRepository $repository, ApiResponse $apiResponse): JsonResponse
    {
        $objs = $repository->findAll();
        return $apiResponse->apiJsonResponse($objs, User::ADMIN_READ);
    }

    /**
     * Create an devis
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an devis",
     * )
     *
     * @OA\Tag(name="Devis")
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

        $obj = (new ImDevis())
            ->setZipcode($data->zipcode)
            ->setCity($data->city)
            ->setLastname($sanitizeData->sanitizeString($data->lastname))
            ->setFirstname($sanitizeData->sanitizeString($data->firstname))
            ->setPhone($data->phone)
            ->setEmail($email)
            ->setTypeAd("Location")
            ->setTypeBien($typeBiens[$data->typeBien])
            ->setEtat($etats[$data->etat])
            ->setArea($data->area)
            ->setAreaLand($sanitizeData->sanitizeString($data->areaLand))
            ->setNbPiece($data->nbPiece)
            ->setNbRoom($sanitizeData->sanitizeString($data->nbRoom))
            ->setInfos($sanitizeData->sanitizeString($data->infos))
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        if($mailerService->sendMail(
                $settingsService->getEmailContact(),
                "[" . $settingsService->getWebsiteName() ."] Demande de devis",
                "Demande de devis réalisé à partir de " . $settingsService->getWebsiteName(),
                'app/email/immo/devis.html.twig',
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
     * Delete an devis
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Devis")
     *
     * @param DataService $dataService
     * @param ImDevis $obj
     * @return JsonResponse
     */
    public function delete(DataService $dataService, ImDevis $obj): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Admin - Delete a group of devis
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
     * @OA\Tag(name="Devis")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteGroup(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(ImDevis::class, json_decode($request->getContent()));
    }
}
