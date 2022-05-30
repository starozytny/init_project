<?php

namespace App\Controller\Api\Bill;

use App\Entity\Bill\BiItem;
use App\Entity\Bill\BiSociety;
use App\Service\ApiResponse;
use App\Service\Bill\BillService;
use App\Service\Data\Bill\DataItem;
use App\Service\Data\DataService;
use App\Service\FileUploader;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/bill/items", name="api_bill_items_")
 */
class ItemController extends AbstractController
{
    const FOLDER_IMAGES = BiItem::FOLDER_IMAGES;

    private $billService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, BillService $billService)
    {
        $this->billService = $billService;
        $this->doctrine = $doctrine;
    }

    public function submitForm($type, BiItem $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataItem $dataEntity, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->get('data'));

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $society = $em->getRepository(BiSociety::class)->find($data->societyId);
        if(!$society){
            return $apiResponse->apiJsonResponseBadRequest('La société est introuvable, veuillez contacter le support.');
        }

        $obj = $dataEntity->setData($obj, $data, $society);

        $file = $request->files->get('image');
        if($type === "create"){
            if ($file) {
                $fileName = $fileUploader->upload($file, self::FOLDER_IMAGES);
                $obj->setImage($fileName);
            }
        }else{
            if ($file) {
                $fileName = $fileUploader->replaceFile($file, $obj->getImage(), self::FOLDER_IMAGES);
                $obj->setImage($fileName);
            }
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiItem::ITEM_READ);
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
     * @param DataItem $dataEntity
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           DataItem $dataEntity, FileUploader $fileUploader): JsonResponse
    {
        return $this->submitForm("create", new BiItem(), $request, $apiResponse, $validator, $dataEntity, $fileUploader);
    }

    /**
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"POST"})
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
     * @param DataItem $dataEntity
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function update(Request $request, $id, ValidatorService $validator,  ApiResponse $apiResponse,
                           DataItem $dataEntity, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiItem::class)->find($id);
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity, $fileUploader);
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

        $obj = $em->getRepository(BiItem::class)->find($id);
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
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected($this->getUser(), BiItem::class, json_decode($request->getContent()));
    }
}
