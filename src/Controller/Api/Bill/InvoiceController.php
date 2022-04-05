<?php

namespace App\Controller\Api\Bill;

use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiProduct;
use App\Entity\Society;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Bill\BillService;
use App\Service\Data\Bill\DataInvoice;
use App\Service\MailerService;
use App\Service\SettingsService;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Mpdf\MpdfException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api/bill/invoices", name="api_bill_invoices_")
 */
class InvoiceController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @throws Exception
     */
    public function submitForm($type, BiInvoice $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataInvoice $dataEntity): JsonResponse
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

        //OLD products
        $products = $em->getRepository(BiProduct::class)->findBy(['identifiant' => $obj->getIdentifiant()]);
        if(count($products) != 0){
            foreach($products as $pr){
                $em->remove($pr);
            }
        }

        //NEW products
        $products = [];
        foreach($data->products as $pr){
            $products[] = $dataEntity->setDataProduct(new BiProduct(), $pr);
        }

        $obj = $dataEntity->setDataInvoice($obj, $data, $society);

        if($type == "create"){
            $obj->setNumero("Z-Brouillon");
        }else{
            $obj->setUpdatedAt(new \DateTime());
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        /** @var BiProduct $product */
        foreach($products as $product){
            $product = ($product)
                ->setType(BiProduct::TYPE_INVOICE)
                ->setIdentifiant($obj->getIdentifiant())
                ->setSociety($society)
            ;

            $em->persist($product);
        }
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiInvoice::INVOICE_READ);
    }

    /**
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
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
     * @OA\Tag(name="Invoices")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataInvoice $dataEntity
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse, DataInvoice $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new BiInvoice(), $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"PUT"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
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
     * @OA\Tag(name="Invoices")
     *
     * @param Request $request
     * @param BiInvoice $obj
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataInvoice $dataEntity
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request, BiInvoice $obj, ValidatorService $validator,  ApiResponse $apiResponse, DataInvoice $dataEntity): JsonResponse
    {
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
     * @OA\Tag(name="Invoices")
     *
     * @param BiInvoice $obj
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function delete(BiInvoice $obj, ApiResponse $apiResponse): JsonResponse
    {
        if($obj->getStatus() !== BiInvoice::STATUS_DRAFT){
            return $apiResponse->apiJsonResponseBadRequest("Vous ne pouvez pas supprimer une facture établie.");
        }

        $em = $this->doctrine->getManager();

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
     * @OA\Tag(name="Invoices")
     *
     * @param BiInvoice $obj
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function duplicate(BiInvoice $obj, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $products = $em->getRepository(BiProduct::class)->findBy(['identifiant' => $obj->getIdentifiant()]);

        $createdAt = new \DateTime();
        $createdAt->setTimezone(new \DateTimeZone("Europe/Paris"));

        $newObj = clone $obj;
        $newObj = ($newObj)
            ->setStatus(BiInvoice::STATUS_DRAFT)
            ->setNumero("Z-Brouillon")
            ->setCreatedAt($createdAt)
            ->setUpdatedAt(null)
        ;
        $em->persist($newObj);
        $em->flush();

        foreach($products as $product){
            $pr = clone $product;
            $pr = ($pr)
                ->setUid(uniqid())
                ->setIdentifiant($newObj->getIdentifiant())
            ;

            $em->persist($pr);
        }

        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful(true);
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
     * @OA\Tag(name="Invoices")
     *
     * @param Request $request
     * @param BiInvoice $obj
     * @param ApiResponse $apiResponse
     * @param DataInvoice $dataInvoice
     * @param MailerService $mailerService
     * @param SettingsService $settingsService
     * @return JsonResponse
     * @throws Exception
     */
    public function generate(Request $request, BiInvoice $obj, ApiResponse $apiResponse, DataInvoice $dataInvoice,
                             MailerService $mailerService, SettingsService $settingsService): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        /** @var User $user */
        $user = $this->getUser();
        $obj = $dataInvoice->setDataInvoiceGenerated($obj, $data, $user->getSociety());

        if($mailerService->sendMail(
                $obj->getToEmail(),
                "[" . $settingsService->getWebsiteName() ."] Facture",
                "Facture venant de " . $settingsService->getWebsiteName(),
                'app/email/bill/invoice.html.twig',
                [
                    'elem' => $obj,
                    'user' => $user,
                    'settings' => $settingsService->getSettings(),
                    'urlLogin' => $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'url' => $this->generateUrl('user_invoice', ['id' => $obj->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                ]
            ) != true)
        {
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez contacter le support."
            ]]);
        }

        $obj->setIsSent(true);

        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiInvoice::INVOICE_READ);
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
     * @OA\Tag(name="Invoices")
     *
     * @param BiInvoice $obj
     * @param ApiResponse $apiResponse
     * @param BillService $billService
     * @return JsonResponse
     * @throws MpdfException
     */
    public function download(BiInvoice $obj, ApiResponse $apiResponse, BillService $billService): JsonResponse
    {
        $billService->getInvoice([$obj]);

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }
}
