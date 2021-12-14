<?php

namespace App\Controller\Api\Formation;

use App\Entity\Formation\FoRegistration;
use App\Entity\Formation\FoSession;
use App\Entity\Formation\FoWorker;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\ValidatorService;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/registration", name="api_registration_")
 */
class RegistrationController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function submitForm($type, FoSession $session, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $workers = $em->getRepository(FoWorker::class)->findBy(['id' => $data->workersId]);

        foreach($workers as $worker){

            if($type == "create"){
                $obj = new FoRegistration();
            }

            $obj = ($obj)
                ->setUser($user)
                ->setFormation($session->getFormation())
                ->setSession($session)
                ->setWorker($worker)
            ;

            if($type == "update"){
                $obj->setUpdatedAt(new DateTime());
            }

            $em->persist($obj);
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Create registration worker-session
     *
     * @Route("/{session}", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param Request $request
     * @param FoSession $session
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function create(Request $request, FoSession $session, ValidatorService $validator, ApiResponse $apiResponse): JsonResponse
    {
        return $this->submitForm("create", $session, $request, $apiResponse, $validator);
    }

    /**
     * Switch attestation
     *
     * @Route("/attestation/{id}", name="switch_attestation", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param FoRegistration $obj
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function switchAttestation(FoRegistration $obj, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj->setHaveAttestation(!$obj->getHaveAttestation());
        $em->flush();

        return  $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }
}
