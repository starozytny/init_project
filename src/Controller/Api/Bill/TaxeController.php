<?php

namespace App\Controller\Api\Bill;

use App\Entity\Bill\BiTaxe;
use App\Entity\Society;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\Bill\DataTaxe;
use App\Service\Data\DataService;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/bill/taxes", name="api_bill_taxes_")
 */
class TaxeController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function submitForm($type, BiTaxe $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataTaxe $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $society = $em->getRepository(Society::class)->find($data->societyId);
        if(!$society){
            return $apiResponse->apiJsonResponseBadRequest('La société est introuvable, veuillez contacter le support.');
        }

        $existe = $em->getRepository(BiTaxe::class)->findOneBy(['code' => $data->code]);
        if($existe && $existe->getId() != $obj->getId()){
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => "code",
                'message' => "Ce code est déjà utilisé."
            ]]);
        }

        $obj = $dataEntity->setData($obj, $data, $society);

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::USER_READ);
    }

    /**
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new object"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataTaxe $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataTaxe $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new BiTaxe(), $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an object"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param Request $request
     * @param BiTaxe $obj
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataTaxe $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, BiTaxe $obj, ValidatorService $validator,  ApiResponse $apiResponse, DataTaxe $dataEntity): JsonResponse
    {
        if($obj->getIsNatif()){
            return $apiResponse->apiJsonResponseBadRequest('Vous ne pouvez pas modifier cette taxe.');
        }
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param BiTaxe $obj
     * @param DataService $dataService
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function delete(BiTaxe $obj, DataService $dataService, ApiResponse $apiResponse): JsonResponse
    {
        if($obj->getIsNatif()){
            return $apiResponse->apiJsonResponseBadRequest('Vous ne pouvez pas modifier cette taxe.');
        }
        return $dataService->delete($obj);
    }
}
