<?php

namespace App\Controller\Api\Paiement;

use App\Entity\Paiement\PaBank;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
use App\Service\Data\DataUser;
use App\Service\Data\Paiement\DataBank;
use App\Service\Export;
use App\Service\FileUploader;
use App\Service\MailerService;
use App\Service\NotificationService;
use App\Service\SettingsService;
use App\Service\ValidatorService;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api/banks", name="api_banks_")
 */
class BankController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function submitForm($type, PaBank $obj, Request $request, ApiResponse $apiResponse, ValidatorService $validator,
                               DataBank $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        if (!isset($data->username) || !isset($data->email) || !isset($data->firstname) || !isset($data->lastname)) {
            return $apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $obj = $dataEntity->setData($obj, $data, $user);

        if($type === "update"){
            $obj->setUpdatedAt(new DateTime());
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Create a bank
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new bank object"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Bank")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataBank $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataBank $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new PaBank(), $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * Update a bank
     *
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an bank object"
     * )
     *
     * @OA\Tag(name="Bank")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param PaBank $obj
     * @param DataBank $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           PaBank $obj, DataBank $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * Delete a bank
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Bank")
     *
     * @param PaBank $obj
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function delete(PaBank $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }
}
