<?php

namespace App\Controller\Api\Bill;

use App\Entity\User;
use App\Entity\Bill\BiProduct;
use App\Entity\Bill\BiQuotation;
use App\Entity\Bill\BiSociety;
use App\Service\ApiResponse;
use App\Service\Bill\BillService;
use App\Service\Data\Bill\DataBill;
use App\Service\MailerService;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Mpdf\MpdfException;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * @Route("/api/bill/quotations", name="api_bill_quotations_")
 */
class QuotationController extends AbstractController
{
    private $billService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, BillService $billService)
    {
        $this->billService = $billService;
        $this->doctrine = $doctrine;
    }

    /**
     * @throws Exception
     */
    public function submitForm($type, BiQuotation $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataBill $dataEntity): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();

        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $toGenerate = (bool)$data->toGenerate;

        $society = $em->getRepository(BiSociety::class)->find($data->societyId);
        if(!$society){
            return $apiResponse->apiJsonResponseBadRequest('La société est introuvable, veuillez contacter le support.');
        }

        //OLD products
        $this->billService->removeProducts($user, $obj);

        //NEW products
        $products = [];
        foreach($data->products as $pr){
            $products[] = $dataEntity->setDataProduct(new BiProduct(), $pr, $society);
        }

        $obj = $dataEntity->setDataQuotation($obj, $data, $society);
        if($type == "update"){
            $obj->setUpdatedAt(new \DateTime());
        }

        if($toGenerate){
            $obj = ($obj)
                ->setNumero($dataEntity->createNumero('quotation', $obj->getDateAt(), $society))
                ->setStatus(BiQuotation::STATUS_PROCESSING)
            ;
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);

        $this->billService->updateProductIdentifiant($user, $obj, $products, BiProduct::TYPE_QUOTATION);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiQuotation::QUOTATION_READ);
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
     * @throws Exception
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataBill $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new BiQuotation(), $request, $apiResponse, $validator, $dataEntity);
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
     * @throws Exception
     */
    public function update(Request $request, $id, ValidatorService $validator,  ApiResponse $apiResponse, DataBill $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * @Route("/delete/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function delete($id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);
        if($obj->getStatus() !== BiQuotation::STATUS_DRAFT){
            return $apiResponse->apiJsonResponseBadRequest("Vous ne pouvez pas supprimer un devis établi.");
        }

        $products = $em->getRepository(BiProduct::class)->findBy(['identifiant' => $obj->getIdentifiant()]);
        foreach($products as $product){
            $em->remove($product);
        }

        $em->remove($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful(true);
    }

    /**
     * @Route("/duplicate/{id}", name="duplicate", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function duplicate($id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);
        $products = $em->getRepository(BiProduct::class)->findBy(['identifiant' => $obj->getIdentifiant()]);

        $createdAt = new \DateTime();
        $createdAt->setTimezone(new \DateTimeZone("Europe/Paris"));

        $newObj = clone $obj;
        $newObj = ($newObj)
            ->setStatus(BiQuotation::STATUS_DRAFT)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt(null)
            ->setUid(Uuid::v4())
            ->setInvoiceId(null)
            ->setRefInvoice(null)
            ->setIsArchived(false);
        ;
        $em->persist($newObj);
        $em->flush();

        foreach($products as $product){
            $pr = clone $product;
            $pr = ($pr)
                ->setUid(Uuid::v4())
                ->setIdentifiant($newObj->getIdentifiant())
            ;

            $em->persist($pr);
        }

        $em->flush();

        return $apiResponse->apiJsonResponse($newObj, BiQuotation::QUOTATION_READ);
    }

    /**
     * @Route("/generate/{id}", name="generate", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @param DataBill $dataEntity
     * @param MailerService $mailerService
     * @return JsonResponse
     * @throws Exception
     */
    public function generate($id, ApiResponse $apiResponse, DataBill $dataEntity,
                             MailerService $mailerService): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);
        $obj = $dataEntity->setDataQuotationGenerated($obj);
        $obj->setIsSent(true);

        if(!$mailerService->sendQuotation($obj)){
            $obj->setIsSent(false);
            $em->flush();

            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez contacter le support."
            ]]);
        }
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiQuotation::QUOTATION_READ);
    }

    /**
     * @Route("/archive/{id}", name="archive", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function archive($id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);

        $obj->setIsArchived(!$obj->getIsArchived());
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiQuotation::QUOTATION_READ);
    }

    /**
     * @Route("/answer/{id}/{answer}", name="answer", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param $answer
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function answer($id, $answer, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);

        $status = $obj->getStatus();
        switch ($answer){
            case 0:
                $status = BiQuotation::STATUS_REFUSED;
                $obj->setIsArchived(true);
                break;
            case 1:
                $status = BiQuotation::STATUS_ACCEPTED;
                break;
            case 2:
                $status = BiQuotation::STATUS_PROCESSING;
                break;
            default:
                break;
        }

        $obj->setStatus($status);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiQuotation::QUOTATION_READ);
    }

    /**
     * @Route("/envoyer/{id}", name="send", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @param MailerService $mailerService
     * @return JsonResponse
     */
    public function send($id, ApiResponse $apiResponse, MailerService $mailerService): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);

        $obj->setIsSent(true);
        if(!$mailerService->sendQuotation($obj)){

            $obj->setIsSent(false);
            $em->flush();

            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez contacter le support."
            ]]);
        }

        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiQuotation::QUOTATION_READ);
    }

    /**
     * @Route("/download/{id}", name="download", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     *
     * @OA\Tag(name="Bill")
     *
     * @param $id
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     * @throws MpdfException
     */
    public function download($id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiQuotation::class)->find($id);
        $this->billService->getQuotation($this->getUser(), [$obj]);

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }
}
