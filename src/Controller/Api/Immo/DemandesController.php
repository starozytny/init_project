<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImBien;
use App\Entity\Immo\ImDemande;
use App\Entity\User;
use App\Repository\Immo\ImDemandeRepository;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
use App\Service\MailerService;
use App\Service\SanitizeData;
use App\Service\SettingsService;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/immo/demandes", name="api_immo_demandes_")
 */
class DemandesController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Admin - Get array of demandes
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="index", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns array of demandes",
     * )
     * @OA\Tag(name="Demandes")
     *
     * @param ImDemandeRepository $repository
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function index(ImDemandeRepository $repository, ApiResponse $apiResponse): JsonResponse
    {
        $objs = $repository->findAll();
        return $apiResponse->apiJsonResponse($objs, User::ADMIN_READ);
    }

    /**
     * Create a demande
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Demandes")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param MailerService $mailerService
     * @param SettingsService $settingsService
     * @param SanitizeData $sanitizeData
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           MailerService $mailerService, SettingsService $settingsService, SanitizeData $sanitizeData): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        if (!isset($data->name) || !isset($data->email) || !isset($data->message) || !isset($data->bien)) {
            return $apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        $bien = $em->getRepository(ImBien::class)->find($data->bien);
        if(!$bien){
            return $apiResponse->apiJsonResponseBadRequest("Ce bien n'existe pas.");
        }

        $obj = (new ImDemande())
            ->setName($sanitizeData->sanitizeString($data->name))
            ->setPhone($data->phone)
            ->setEmail($data->email)
            ->setMessage($sanitizeData->sanitizeString($data->message))
            ->setBien($bien)
            ->setLabel($bien->getLabel())
            ->setAddress($bien->getAddress() ? $bien->getAddress()->getAddress() : "Inconnue")
            ->setShortAddress($bien->getAddress()->getShortAddress() ? $bien->getAddress()->getShortAddress() : "Inconnue")
            ->setRealRef($bien->getRealRef())
            ->setBienIdentifiant($bien->getIdentifiant())
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        if($mailerService->sendMail(
                $settingsService->getEmailContact(),
                "[" . $settingsService->getWebsiteName() ."] Demande d'informations",
                "Demande d'informations réalisé à partir de " . $settingsService->getWebsiteName(),
                'app/email/immo/demande.html.twig',
                ['contact' => $obj, 'settings' => $settingsService->getSettings()]
            ) != true)
        {
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez contacter le support."
            ]]);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Admin - Change isSeen to true
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/{id}/is-seen", name="isSeen", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns demande object",
     * )
     *
     * @OA\Tag(name="Demandes")
     *
     * @param ImDemande $obj
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function isSeen(ImDemande $obj, DataService $dataService): JsonResponse
    {
        return $dataService->isSeenToTrue($obj);
    }

    /**
     * Admin - Delete a demande
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Demandes")
     *
     * @param DataService $dataService
     * @param ImDemande $obj
     * @return JsonResponse
     */
    public function delete(DataService $dataService, ImDemande $obj): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Admin - Delete a group of demandes
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
     * @OA\Tag(name="Demandes")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteGroup(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(ImDemande::class, json_decode($request->getContent()));
    }
}
