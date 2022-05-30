<?php

namespace App\Controller\Api\Bill;

use App\Entity\User;
use App\Entity\Bill\BiContract;
use App\Entity\Bill\BiContractCustomer;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiProduct;
use App\Entity\Bill\BiSite;
use App\Entity\Bill\BiSociety;
use App\Service\ApiResponse;
use App\Service\Bill\BillService;
use App\Service\Data\Bill\DataBill;
use App\Service\Data\Bill\DataContract;
use App\Service\MailerService;
use App\Service\ValidatorService;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Http\Discovery\Exception\NotFoundException;
use Mpdf\MpdfException;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/bill/contracts", name="api_bill_contracts_")
 */
class ContractController extends AbstractController
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
    public function submitForm($type, BiContract $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataContract $dataEntity, DataBill $dataBill): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();

        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $society = $em->getRepository(BiSociety::class)->find($data->societyId);
        if(!$society){
            return $apiResponse->apiJsonResponseBadRequest('La société est introuvable, veuillez contacter le support.');
        }

        //OLD products
        $this->billService->removeProducts($user, $obj);

        //NEW products
        $products = [];
        foreach($data->products as $pr){
            $products[] = $dataBill->setDataProduct(new BiProduct(), $pr, $society);
        }

        //SET data
        $obj = $dataEntity->setDataContract($obj, $data, $society);

        if($type === "create"){
            $obj->setNumero($dataBill->createNumero("contract", new \DateTime(), $society));
        }else{
            $obj->setUpdatedAt(new \DateTime());
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);

        $this->billService->updateProductIdentifiant($user, $obj, $products, BiProduct::TYPE_CONTRACT);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiContract::CONTRACT_READ);
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
     * @param DataContract $dataEntity
     * @param DataBill $dataBill
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           DataContract $dataEntity, DataBill $dataBill): JsonResponse
    {
        return $this->submitForm("create", new BiContract(), $request, $apiResponse, $validator, $dataEntity, $dataBill);
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
     * @param DataContract $dataEntity
     * @param DataBill $dataBill
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request, $id, ValidatorService $validator, ApiResponse $apiResponse,
                           DataContract $dataEntity, DataBill $dataBill): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiContract::class)->find($id);
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity, $dataBill);
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
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function delete($id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiContract::class)->find($id);
        foreach($obj->getContractCustomers() as $relation){
            $em->remove($relation);
        }

        $em->remove($obj);
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("good");
    }

    /**
     * @Route("/relation/link/{type}/{id}/{customer}", name="relation_link", options={"expose"=true}, methods={"POST"})
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
     * @param $type
     * @param $id
     * @param $customer
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataContract $dataEntity
     * @return JsonResponse
     */
    public function relation(Request $request, $type, $id, $customer, ValidatorService $validator,
                                   ApiResponse $apiResponse, DataContract $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();

        if($type == "customer"){
            $site = null;
            $customer = $em->getRepository(BiCustomer::class)->find($customer);
        }else{
            $site = $em->getRepository(BiSite::class)->find($customer);
            $customer = $site->getCustomer();
        }
        $obj = $em->getRepository(BiContract::class)->find($id);

        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $society = $em->getRepository(BiSociety::class)->find($data->societyId);
        if(!$society){
            return $apiResponse->apiJsonResponseBadRequest('La société est introuvable, veuillez contacter le support.');
        }

        if($existe = $em->getRepository(BiContractCustomer::class)->findOneBy(['contract' => $obj, 'customer' => $customer, 'site' => $site])){
            $oldId = $existe->getId();

            $em->remove($existe);
            $em->flush();

            return $apiResponse->apiJsonResponseCustom($oldId);
        }else{
            $relation = $dataEntity->setDataRelation(new BiContractCustomer(), $data, $obj, $customer, $site);

            $noErrors = $validator->validate($relation);
            if ($noErrors !== true) {
                return $apiResponse->apiJsonResponseValidationFailed($noErrors);
            }

            $em->persist($relation);

            $em->flush();

            return $apiResponse->apiJsonResponse($relation, BiContractCustomer::RELATION_READ);
        }
    }

    /**
     * @Route("/relation/active/{id}", name="relation_active", options={"expose"=true}, methods={"POST"})
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
    public function relationActive($id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiContractCustomer::class)->find($id);

        $obj->setIsActive(!$obj->getIsActive());
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiContractCustomer::RELATION_READ);
    }

    /**
     * @Route("/relation/preview/{year}/{month}/{id}", name="relation_preview", options={"expose"=true}, methods={"GET"})
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
     * @param $year
     * @param $month
     * @param $id
     * @param ApiResponse $apiResponse
     * @param DataBill $dataBill
     * @return JsonResponse
     * @throws MpdfException
     * @throws Exception
     */
    public function preview($year, $month, $id, ApiResponse $apiResponse, DataBill $dataBill): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiContractCustomer::class)->find($id);
        if(!$obj->getNumero()){
            return $apiResponse->apiJsonResponseBadRequest("Ce contrat n'a pas de numéro.");
        }

        $invoice = $this->createDraftInvoice($year, $month, $obj, $dataBill);

        $this->billService->getInvoice($this->getUser(), [$invoice]);

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }

    /**
     * @Route("/relation/numero/{id}", name="relation_update_numero", options={"expose"=true}, methods={"PUT"})
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
     * @param Request $request
     * @param $id
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function relationUpdateNumero(Request $request, $id, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        $obj = $em->getRepository(BiContractCustomer::class)->find($id);

        $numero = $data->numero ? trim($data->numero) : null;
        if(!$numero){
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => "numero",
                'message' => "Ce numéro n'est pas valide."
            ]]);
        }

        $existe = $em->getRepository(BiContractCustomer::class)->findOneBy(['numero' => $numero]);
        if($existe && $existe->getId() != $obj->getId()){
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => "numero",
                'message' => "Ce numéro existe déjà."
            ]]);
        }

        $obj->setNumero($numero);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, BiContractCustomer::RELATION_READ);
    }

    /**
     * @Route("/invoice/create/{year}/{month}/{id}", name="invoice_create", options={"expose"=true}, methods={"POST"})
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
     * @param $year
     * @param $month
     * @param $id
     * @param ApiResponse $apiResponse
     * @param DataBill $dataBill
     * @return JsonResponse
     * @throws Exception
     */
    public function createInvoice($year, $month, $id, ApiResponse $apiResponse, DataBill $dataBill): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiContractCustomer::class)->find($id);
        if(!$obj->getNumero()){
            return $apiResponse->apiJsonResponseBadRequest("Ce contrat n'a pas de numéro.");
        }

        $invoice = $this->createDraftInvoice($year, $month, $obj, $dataBill);

        return $apiResponse->apiJsonResponse($invoice, BiInvoice::CONTRACT_READ);
    }

    /**
     * @Route("/invoice/update/{year}/{month}/{id}", name="invoice_update", options={"expose"=true}, methods={"GET"})
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
     * @param $year
     * @param $month
     * @param $id
     * @param DataBill $dataBill
     * @return RedirectResponse
     * @throws Exception
     */
    public function updateInvoice($year, $month, $id, DataBill $dataBill): RedirectResponse
    {
        $em = $this->doctrine->getManager();

        $obj = $em->getRepository(BiContractCustomer::class)->find($id);
        if(!$obj->getNumero()){
            return $this->redirectToRoute('user_bill_contracts_index');
        }

        $invoice = $this->createDraftInvoice($year, $month, $obj, $dataBill);

        return $this->redirectToRoute('user_bill_invoice_update', ["id" => $invoice->getId()]);
    }

    /**
     * @Route("/invoice/generate/{year}/{month}/{id}", name="invoice_generate", options={"expose"=true}, methods={"POST"})
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
     * @param Request $request
     * @param $year
     * @param $month
     * @param $id
     * @param ApiResponse $apiResponse
     * @param DataBill $dataBill
     * @param MailerService $mailerService
     * @return JsonResponse
     * @throws Exception
     */
    public function generate(Request $request, $year, $month, $id, ApiResponse $apiResponse,
                             DataBill $dataBill, MailerService $mailerService): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        $obj = $em->getRepository(BiContractCustomer::class)->find($id);
        if(!$obj->getNumero()){
            return $apiResponse->apiJsonResponseBadRequest("Ce contrat n'a pas de numéro.");
        }

        $invoice = $this->createDraftInvoice($year, $month, $obj, $dataBill);

        return $this->billService->generateAndSendInvoice($invoice, $data, $this->getUser(), $dataBill, $apiResponse, $mailerService, $year, $month);
    }

    /**
     * @Route("/invoices/generate/{year}/{month}", name="invoices_generate", options={"expose"=true}, methods={"POST"})
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
     * @param Request $request
     * @param $year
     * @param $month
     * @param ApiResponse $apiResponse
     * @param DataBill $dataBill
     * @param MailerService $mailerService
     * @return JsonResponse
     * @throws Exception
     */
    public function generates(Request $request, $year, $month, ApiResponse $apiResponse,
                              DataBill $dataBill, MailerService $mailerService): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if($data == null){
            return $apiResponse->apiJsonResponseBadRequest('Données vide.');
        }

        $society = $this->billService->getSociety($user);

        // for search and reduce queries
        $nProducts  = $em->getRepository(BiProduct::class)->findBy(['type' => BiProduct::TYPE_CONTRACT]);
        $nInvoices  = $em->getRepository(BiInvoice::class)->findWithContractBySociety($society);

        $invoices  = $em->getRepository(BiInvoice::class)->findBy(['id' => $data->invoicesId]);
        $relations = $em->getRepository(BiContractCustomer::class)->findBy(['id' => $data->relationsId]);

        $toSend = [];
        foreach($data->data as $item){
            foreach($item->elements as $element){
                if($element->isValid){
                    if($element->type === "invoice"){

                        foreach($invoices as $invoice){
                            if($invoice->getId() == $element->id){
                                if($invoice->getStatus() === BiInvoice::STATUS_DRAFT){
                                    $invoice = $this->billService->generateInvoice($invoice, $element, $user, $dataBill, $year, $month, $relations);

                                    $toSend[] = $invoice;
                                }
                            }
                        }

                    }else{

                        foreach($relations as $relation){
                            if($relation->getId() == $element->id){
                                $invoice = $this->createDraftInvoice($year, $month, $relation, $dataBill, $society, false, $nInvoices, $nProducts);
                                $invoice = $this->billService->generateInvoice($invoice, $element, $user, $dataBill, $year, $month, $relations);

                                $toSend[] = $invoice;
                            }
                        }

                    }
                }
            }
        }
        $society->setDateContract(new DateTime($year . '-' . $month . '-01'));

        $em->flush();

        $i = 1;
        foreach($toSend as $invoice){
            $invoice->setIsSent(true);

            if(!$mailerService->sendInvoice($invoice)){
                $invoice->setIsSent(false);
            }

            $i++;
            if($i%20 == 0){
                sleep(1);
            }
        }

        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }

    /**
     * @throws Exception
     */
    private function createDraftInvoice($year, $month, BiContractCustomer $obj, DataBill $dataBill, ?BiSociety $society = null,
                                        $flushProduct = true, $nInvoices = null, $nProducts = null)
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();

        if(!$obj->getNumero()){
            throw new NotFoundException("Numéro de contrat inexistant. [" . $obj->getId() . "].");
        }

        $society  = $society ?: $this->billService->getSociety($user);
        $contract = $obj->getContract();
        $refRelation = $year.$month;
        $numRelation = $obj->getNumero();

        if($nInvoices){
            /** @var BiInvoice $inv */
            foreach ($nInvoices as $inv){
                if($inv->getContractId() == $contract->getId()
                    && $inv->getRelationId() == $obj->getId()
                    && $inv->getRefRelation() == $refRelation
                    && $inv->getNumRelation() == $numRelation
                ){
                    return $inv;
                }
            }
        }else{
            if($existe = $em->getRepository(BiInvoice::class)->findOneBy([
                'contractId' => $contract->getId(),
                'relationId' => $obj->getId(),
                'refRelation' => $refRelation,
                'numRelation' => $numRelation
            ])){
                return $existe;
            }
        }

        if($nProducts){
            $products = [];
            /** @var BiProduct $pr */
            foreach($nProducts as $pr){
                if($pr->getIdentifiant() == $contract->getIdentifiant()){
                    $products[] = $pr;
                }
            }
        }else{
            $products = $em->getRepository(BiProduct::class)->findBy(['identifiant' => $contract->getIdentifiant()]);
        }

        //create invoice draft then, can modify
        $invoice = $dataBill->setDataInvoiceFromContract(new BiInvoice(), $obj, $refRelation, $contract, $society);

        $dateAt = $contract->getDateAt();
        $nDateAt = new DateTime($year . '-' . $month . '-' . $dateAt->format('d'));

        $dueAt  = $contract->getDueAt();
        if($dueAt){
            $interval = date_diff($dateAt, $dueAt);

            $dueAt = clone $nDateAt;
            $dueAt->modify('+' . $interval->y . ' years');
            $dueAt->modify('+' . $interval->m . ' months');
            $dueAt->modify('+' . $interval->d . ' days');
        }

        $next = clone $nDateAt;
        $noteProduct = "Selon contrat N°" . $obj->getNumero() . ' <br/> ';

        switch ($contract->getPeriod()){
            case BiContract::PERIOD_SEME:
                $next = date_add($next, date_interval_create_from_date_string('5 months'));
                $next = $next->modify('last day of this month');
                $noteProduct .= "Du 01/" . $nDateAt->format('m/Y') . " au " . $next->format('d/m/Y');
                break;
            case BiContract::PERIOD_TRIM:
                $next = date_add($next, date_interval_create_from_date_string('2 months'));
                $next = $next->modify('last day of this month');
                $noteProduct .= "Du 01/" . $nDateAt->format('m/Y') . " au " . $next->format('d/m/Y');
                break;
            case BiContract::PERIOD_MENS:
                $next = $next->modify('last day of this month');
                $noteProduct .= "Du 01/" . $nDateAt->format('m/Y') . " au " . $next->format('d/m/Y');
                break;
            default:
                $noteProduct .= "Pour l'année " . $nDateAt->format('Y');
                break;
        }

        ($invoice)
            ->setDateAt($nDateAt)
            ->setDueAt($dueAt)
            ->setNoteProduct($noteProduct)
            ->setIsHidden(true)
        ;

        $em->persist($invoice);

        $nProducts = [];
        foreach($products as $product){
            $nProducts[] = clone $product;
        }

        $this->billService->updateProductIdentifiant($user, $invoice, $nProducts, BiProduct::TYPE_INVOICE);
        if($flushProduct){
            $em->flush();
        }

        return $invoice;
    }
}
