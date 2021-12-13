<?php

namespace App\Controller\Api\Formation;

use App\Entity\Formation\FoFormation;
use App\Entity\Formation\FoRegistration;
use App\Entity\Formation\FoWorker;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\DataFormation;
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

    public function submitForm($type, FoRegistration $obj, FoFormation $formation, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataFormation $dataEntity): JsonResponse
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
            $obj = ($obj)
                ->setUser($user)
                ->setFormation($formation)
                ->setWorker($worker)
            ;
        }

        if($type == "update"){
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
     * Create registration worker-formation
     *
     * @Route("/{formation}", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param Request $request
     * @param FoFormation $formation
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataFormation $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, FoFormation $formation, ValidatorService $validator, ApiResponse $apiResponse,
                           DataFormation $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new FoRegistration(), $formation, $request, $apiResponse, $validator, $dataEntity);
    }
}
