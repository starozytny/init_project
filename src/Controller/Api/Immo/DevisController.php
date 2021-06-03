<?php

namespace App\Controller\Api\Immo;

use App\Entity\Contact;
use App\Entity\Immo\ImAlert;
use App\Entity\Immo\ImDevis;
use App\Entity\Immo\ImEstimation;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\Immo\ImAlertRepository;
use App\Repository\Immo\ImDevisRepository;
use App\Repository\Immo\ImEstimationRepository;
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
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, SanitizeData $sanitizeData, ApiResponse $apiResponse): JsonResponse
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
            ->setTypeAd($typeAd)
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
     * @param ApiResponse $apiResponse
     * @param ImDevis $obj
     * @return JsonResponse
     */
    public function delete(ApiResponse $apiResponse, ImDevis $obj): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Supression réussie !");
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
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function deleteGroup(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        $objs = $em->getRepository(ImDevis::class)->findBy(['id' => $data]);

        if ($objs) {
            foreach ($objs as $item) {
                $em->remove($item);
            }
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("Supression de la sélection réussie !");
    }
}
