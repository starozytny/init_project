<?php

namespace App\Controller\Api\Formation;

use App\Entity\Formation\FoFormation;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\DataFormation;
use App\Service\Data\DataService;
use App\Service\ValidatorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/formations", name="api_formations_")
 */
class FormationController extends AbstractController
{
    /**
     * Create a formation
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Formations")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataFormation $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           DataFormation $dataEntity): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->get('data'));

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setData(new FoFormation(), $data);

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }


    /**
     * Switch is published
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/formation/{id}", name="formation_published", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a formation object",
     * )
     *
     * @OA\Tag(name="Formations")
     *
     * @param DataService $dataService
     * @param FoFormation $obj
     * @return JsonResponse
     */
    public function switchIsPublished(DataService $dataService, FoFormation $obj): JsonResponse
    {
        return $dataService->switchIsPublished($obj);
    }

    /**
     * Admin - Delete a formation
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
     * @OA\Tag(name="Formations")
     *
     * @param FoFormation $obj
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function delete(FoFormation $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Admin - Delete a group of formations
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
     * @OA\Tag(name="Formations")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(FoFormation::class, json_decode($request->getContent()));
    }
}
