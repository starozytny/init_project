<?php

namespace App\Controller\Api\Immo;

use App\Entity\Contact;
use App\Entity\Immo\ImAlert;
use App\Entity\Immo\ImEstimation;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\Immo\ImAlertRepository;
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

        if (!isset($data->email) || !isset($data->typeAd) || !isset($data->typeBien)) {
            return $apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        $email = trim($data->email);
        $typeAd = $data->typeAd === "0" ? "Location" : "Vente";
        $typeBien = $data->typeBien;

        $obj = (new ImEstimation())
            ->setEmail($email)
            ->setTypeAd($typeAd)
            ->setTypeBien($typeBien)
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Estimation enregistrée.");
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
     * @param ApiResponse $apiResponse
     * @param ImEstimation $estimation
     * @return JsonResponse
     */
    public function delete(ApiResponse $apiResponse, ImEstimation $estimation): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($estimation);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Supression réussie !");
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
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function deleteGroup(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        $objs = $em->getRepository(ImEstimation::class)->findBy(['id' => $data]);

        if ($objs) {
            foreach ($objs as $item) {
                $em->remove($item);
            }
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("Supression de la sélection réussie !");
    }
}
