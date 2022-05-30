<?php

namespace App\Controller\Api\Bill;

use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiSite;
use App\Entity\Bill\BiSociety;
use App\Service\ApiResponse;
use App\Service\Bill\BillService;
use App\Service\Data\Bill\DataBill;
use App\Service\Data\DataService;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/bill/sites", name="api_bill_sites_")
 */
class SiteController extends AbstractController
{
    private $billService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, BillService $billService)
    {
        $this->billService = $billService;
        $this->doctrine = $doctrine;
    }

    public function submitForm($type, BiSite $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataBill $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $customer = $em->getRepository(BiCustomer::class)->find($data->customer);
        if(!$customer){
            return $apiResponse->apiJsonResponseBadRequest('Le client est introuvable, veuillez contacter le support.');
        }

        $society = $em->getRepository(BiSociety::class)->find($data->societyId);
        if(!$society){
            return $apiResponse->apiJsonResponseBadRequest('La société est introuvable, veuillez contacter le support.');
        }

        $obj = $dataEntity->setDataSite($obj, $data, $customer, $society);
        if($type === "create" && $data->useNumero[0] == 1){
            $obj->setNumero($dataEntity->createNumero("site", new \DateTime(), $society));
        }else{
            $numero = $data->numero ? trim($data->numero) : "";
            $existe = $em->getRepository(BiSite::class)->findOneBy(['numero' => $numero]);
            if($existe && $existe->getId() != $obj->getId()){
                return $apiResponse->apiJsonResponseValidationFailed([[
                    'name' => "numero",
                    'message' => "Ce numéro est déjà attribué."
                ]]);
            }
            $obj->setNumero($numero);
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiSite::SITE_READ);
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
     * @param DataBill $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataBill $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new BiSite(), $request, $apiResponse, $validator, $dataEntity);
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
     * @param $id
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataBill $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, $id, ValidatorService $validator,  ApiResponse $apiResponse, DataBill $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiSite::class)->find($id);
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * @Route("/{id}/dissociate", name="dissociate", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function dissociate($id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiSite::class)->find($id);

        $customer = $obj->getCustomer();
        $customer->removeSite($obj);

        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Good");
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
     * @param $id
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function delete($id, DataService $dataService): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiSite::class)->find($id);

        $customer = $obj->getCustomer();
        $customer->removeSite($obj);

        return $dataService->delete($this->getUser(), $obj);
    }

    /**
     * @Route("/", name="delete_group", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function deleteSelected(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $objs = $em->getRepository(BiCustomer::class)->findBy(['id' => json_decode($request->getContent())]);

        if ($objs) {
            foreach ($objs as $obj) {
                $customer = $obj->getCustomer();
                $customer->removeSite($obj);

                $em->remove($obj);
            }
        }

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("Suppression de la sélection réussie !");
    }
}
