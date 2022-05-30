<?php

namespace App\Controller\Api\Bill;

use App\Entity\User;
use App\Entity\Bill\BiSociety;
use App\Service\ApiResponse;
use App\Service\Bill\BillService;
use App\Service\Data\Bill\DataBill;
use App\Service\FileUploader;
use App\Service\SanitizeData;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/bill/societies", name="api_bill_societies_")
 */
class SocietyController extends AbstractController
{
    const FOLDER_LOGOS = BiSociety::FOLDER_LOGOS;

    private $billService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, BillService $billService)
    {
        $this->billService = $billService;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/data/{id}", name="data", options={"expose"=true}, methods={"GET"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns data"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function data($id, ApiResponse $apiResponse, SerializerInterface $serializer): JsonResponse
    {
        $mainSociety = $this->billService->getMainSociety($id);
        $em = $this->doctrine->getManager();

        $objs = $em->getRepository(BiSociety::class)->findAll();

        $objs = $serializer->serialize($objs, 'json', ['groups' => User::ADMIN_READ]);

        return $apiResponse->apiJsonResponseCustom([
            'societies' => $objs,
        ]);
    }

    public function submitForm($type, BiSociety $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataBill $dataEntity, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->get('data'));

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setDataSociety($obj, $data);

        $file = $request->files->get('logo');
        if($type === "create"){
            if ($file) {
                $fileName = $fileUploader->upload($file, self::FOLDER_LOGOS);
                $obj->setLogo($fileName);
            }
        }else{
            if ($file) {
                $fileName = $fileUploader->replaceFile($file, $obj->getLogo(),self::FOLDER_LOGOS);
                $obj->setLogo($fileName);
            }
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
     * Create a society
     *
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
     * @OA\Tag(name="Societies")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param FileUploader $fileUploader
     * @param DataBill $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           FileUploader $fileUploader, DataBill $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new BiSociety(), $request, $apiResponse, $validator, $dataEntity, $fileUploader);
    }

    /**
     * Update a society
     *
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an object"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Societies")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param $id
     * @param FileUploader $fileUploader
     * @param DataBill $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorService $validator, ApiResponse $apiResponse, $id,
                           FileUploader $fileUploader, DataBill $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiSociety::class)->find($id);
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity, $fileUploader);
    }

    /**
     * @Route("/update-settings/{society}/{id}", name="update_settings", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an object"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Societies")
     *
     * @param Request $request
     * @param $society
     * @param $id
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param SanitizeData $sanitizeData
     * @return JsonResponse
     * @throws Exception
     */
    public function updateSettings(Request $request, $society, $id, ValidatorService $validator, ApiResponse $apiResponse, SanitizeData $sanitizeData): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        $obj = $em->getRepository(BiSociety::class)->find($id);

        $obj = ($obj)
            ->setCounterInvoice((int) $data->counterInvoice)
            ->setCounterQuotation((int) $data->counterQuotation)
            ->setCounterAvoir((int) $data->counterAvoir)
            ->setCounterContract((int) $data->counterContract)
            ->setCounterCustomer((int) $data->counterCustomer)
            ->setYearInvoice((int) $data->yearInvoice)
            ->setYearQuotation((int) $data->yearQuotation)
            ->setYearAvoir((int) $data->yearAvoir)
            ->setYearContract((int) $data->yearContract)
            ->setYearCustomer((int) $data->yearCustomer)
            ->setDateInvoice($sanitizeData->createDate($data->dateInvoice))
            ->setDateContract($sanitizeData->createDate($data->dateContract))
        ;

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }

    /**
     * Delete a society
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Cannot delete me",
     * )
     *
     * @OA\Tag(name="Societies")
     *
     * @param ApiResponse $apiResponse
     * @param $id
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function delete(ApiResponse $apiResponse, $id, FileUploader $fileUploader): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiSociety::class)->find($id);

        /** @var User $user */
        $user = $this->getUser();
        if ($obj === $user->getSociety()){
            return $apiResponse->apiJsonResponseBadRequest('Vous ne pouvez pas vous supprimer.');
        }

        $em->remove($obj);
        $em->flush();

        $fileUploader->deleteFile($obj->getLogo(), self::FOLDER_LOGOS);
        return $apiResponse->apiJsonResponseSuccessful("Suppression réussie !");
    }
}
