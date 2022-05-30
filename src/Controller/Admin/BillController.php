<?php

namespace App\Controller\Admin;

use App\Entity\Society;
use App\Entity\User;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiItem;
use App\Entity\Bill\BiProduct;
use App\Entity\Bill\BiSite;
use App\Entity\Bill\BiTaxe;
use App\Entity\Bill\BiUnity;
use App\Service\Bill\BillService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/facturation", name="admin_bill_")
 */
class BillController extends AbstractController
{
    private $doctrine;
    private $billService;

    public function __construct(ManagerRegistry $doctrine, BillService $billService)
    {
        $this->doctrine = $doctrine;
        $this->billService = $billService;
    }

    /**
     * @Route("/factures", options={"expose"=true}, name="invoices_index")
     */
    public function invoices(Request $request, SerializerInterface $serializer): Response
    {
        $isArchived = $request->query->get('archive');
        $status = $request->query->get('status');

        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();

        $society = $this->billService->getSociety($user);
        $objs    = $em->getRepository(BiInvoice::class)->findBy(['society' => $society, 'isArchived' => (bool)$isArchived, 'isHidden' => false]);

        $objs   = $serializer->serialize($objs, 'json', ['groups' => BiInvoice::INVOICE_READ]);
        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_INVOICE, $serializer);

        return $this->render('user/pages/bill/invoice.html.twig', array_merge($params, ['donnees' => $objs, 'status' => $status]));
    }

    /**
     * @Route("/factures/ajouter-une-facture", name="invoice_create")
     */
    public function invoiceCreate(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $society = $this->billService->getSociety($user);

        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_INVOICE, $serializer);

        return $this->render('user/pages/bill/invoice_create.html.twig', $params);
    }

    /**
     * @Route("/factures/modifier-une-facture/{id}", name="invoice_update")
     */
    public function invoiceUpdate($id, SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $obj = $em->getRepository(BiInvoice::class)->find($id);
        if($obj->getStatus() != BiInvoice::STATUS_DRAFT){
            $this->addFlash('error', 'Vous ne pouvez pas modifier une facture établie.');
            return $this->redirectToRoute('user_bill_invoices_index');
        }

        $society = $this->billService->getSociety($user);

        $obj = $serializer->serialize($obj, 'json', ['groups' => BiInvoice::INVOICE_READ]);
        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_INVOICE, $serializer);

        return $this->render('user/pages/bill/invoice_update.html.twig', array_merge([
            'donnees' => $obj
        ], $params));
    }

    /**
     * @Route("/factures/creer-un-avoir/{id}", options={"expose"=true}, name="invoices_create_avoir")
     */
    public function invoicesCreateAvoir($id, SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society = $this->billService->getSociety($user);

        $elem   = $em->getRepository(BiInvoice::class)->find($id);
        $obj    = $serializer->serialize($elem, 'json', ['groups' => BiInvoice::INVOICE_READ]);
        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_INVOICE, $serializer);

        return $this->render('user/pages/bill/avoir_create_by_invoice.html.twig', array_merge($params, [
            'donnees' => $obj,
            'elem' => $elem,
        ]));
    }

    /**
     * @Route("/factures/creer-un-devis/{id}", options={"expose"=true}, name="invoices_create_quotation")
     */
    public function invoicesCreateQuotation($id, SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society = $this->billService->getSociety($user);

        $elem   = $em->getRepository(BiInvoice::class)->find($id);
        $obj    = $serializer->serialize($elem, 'json', ['groups' => BiInvoice::INVOICE_READ]);
        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_INVOICE, $serializer);

        return $this->render('user/pages/bill/quotation_create_by_invoice.html.twig', array_merge($params, [
            'donnees' => $obj,
            'elem' => $elem,
        ]));
    }

    /**
     * @Route("/devis", options={"expose"=true}, name="quotations_index")
     */
    public function quotations(Request $request, SerializerInterface $serializer): Response
    {
        $isArchived = $request->query->get('archive');
        $status = $request->query->get('status');

        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society = $this->billService->getSociety($user);
        $objs    = $em->getRepository(BiQuotation::class)->findBy(['society' => $society, 'isArchived' => (bool)$isArchived]);

        $objs   = $serializer->serialize($objs, 'json', ['groups' => BiQuotation::QUOTATION_READ]);
        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_QUOTATION, $serializer);

        return $this->render('user/pages/bill/quotation.html.twig', array_merge($params, ['donnees' => $objs, 'status' => $status]));
    }

    /**
     * @Route("/devis/ajouter-un-devis", name="quotation_create")
     */
    public function quotationCreate(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $society = $this->billService->getSociety($user);

        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_QUOTATION, $serializer);

        return $this->render('user/pages/bill/quotation_create.html.twig', $params);
    }

    /**
     * @Route("/devis/creer-une-facture/{id}", options={"expose"=true}, name="quotations_create_invoice")
     */
    public function quotationsCreateInvoice($id, SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $elem = $em->getRepository(BiQuotation::class)->find($id);

        $society = $this->billService->getSociety($user);

        $obj    = $serializer->serialize($elem, 'json', ['groups' => BiQuotation::QUOTATION_READ]);
        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_QUOTATION, $serializer);

        return $this->render('user/pages/bill/invoice_create_by_quotation.html.twig', array_merge($params, [
            'donnees' => $obj,
            'elem' => $elem,
        ]));
    }

    /**
     * @Route("/avoirs", options={"expose"=true}, name="avoirs_index")
     */
    public function avoirs(Request $request, SerializerInterface $serializer): Response
    {
        $isArchived = $request->query->get('archive');

        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society = $this->billService->getSociety($user);
        $objs    = $em->getRepository(BiAvoir::class)->findBy(['society' => $society, 'isArchived' => (bool)$isArchived]);

        $objs   = $serializer->serialize($objs, 'json', ['groups' => BiAvoir::AVOIR_READ]);
        $params = $this->billService->getDataCommonPage($user->getNameManager(), $society, BiProduct::TYPE_AVOIR, $serializer);

        return $this->render('user/pages/bill/avoir.html.twig', array_merge($params, ['donnees' => $objs]));
    }

    /**
     * @Route("/articles", name="items_index")
     */
    public function items(SerializerInterface $serializer, BillService $billService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society = $this->billService->getSociety($user);
        $objs    = $em->getRepository(BiItem::class)->findBy(['society' => $society]);

        $objs = $serializer->serialize($objs, 'json', ['groups' => BiItem::ITEM_READ]);

        [$taxes, $unities] = $billService->getTaxesAndUnitiesData($user->getNameManager(), $society, true);

        return $this->render('user/pages/bill/item.html.twig', [
            'donnees' => $objs,
            'society' => $society,
            'taxes' => $taxes,
            'unities' => $unities,
        ]);
    }

    /**
     * @Route("/clients/liste", name="customers_index")
     */
    public function customers(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society  = $this->billService->getSociety($user);
        $objs     = $em->getRepository(BiCustomer::class)->findBy(['society' => $society]);
        $invoices = $em->getRepository(BiInvoice::class)->findWithCustomerBySociety($society);
        $sites    = $em->getRepository(BiSite::class)->findBy(['society' => $society]);

        $objs     = $serializer->serialize($objs, 'json', ['groups' => BiCustomer::CUSTOMER_READ]);
        $sites    = $serializer->serialize($sites, 'json', ['groups' => BiSite::SITE_READ]);
        $invoices = $serializer->serialize($invoices, 'json', ['groups' => BiInvoice::INVOICE_READ]);

        return $this->render('user/pages/bill/customer.html.twig', [
            'pageName' => "clients",
            'donnees' => $objs,
            'sites' => $sites,
            'invoices' => $invoices,
            'society' => $society
        ]);
    }

    /**
     * @Route("/clients/sites", name="sites_index")
     */
    public function sites(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society    = $this->billService->getSociety($user);
        $objs       = $em->getRepository(BiSite::class)->findBy(['society' => $society]);
        $customers  = $em->getRepository(BiCustomer::class)->findBy(['society' => $society]);

        $objs       = $serializer->serialize($objs, 'json', ['groups' => BiSite::SITE_READ]);
        $customers  = $serializer->serialize($customers, 'json', ['groups' => BiCustomer::CUSTOMER_READ]);

        return $this->render('user/pages/bill/site.html.twig', [
            'pageName' => "sites",
            'donnees' => $objs,
            'customers' => $customers,
            'society' => $society,
        ]);
    }

    /**
     * @Route("/contrats/liste", options={"expose"=true}, name="contracts_index")
     */
    public function contracts(SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society    = $this->billService->getSociety($user);
        $objs       = $em->getRepository(BiContract::class)->findBy(['society' => $society]);
        $relations  = $em->getRepository(BiContractCustomer::class)->findBy(['contract' => $objs]);
        $items      = $em->getRepository(BiItem::class)->findBy(['society' => $society]);
        $products   = $em->getRepository(BiProduct::class)->findBy(['society' => $society, 'type' => BiProduct::TYPE_CONTRACT]);
        $customers  = $em->getRepository(BiCustomer::class)->findBy(['society' => $society]);
        $sites      = $em->getRepository(BiSite::class)->findBy(['society' => $society]);

        $objs       = $serializer->serialize($objs, 'json', ['groups' => BiContract::CONTRACT_READ]);
        $relations  = $serializer->serialize($relations, 'json', ['groups' => BiContractCustomer::RELATION_READ]);
        $items      = $serializer->serialize($items, 'json', ['groups' => BiItem::ITEM_READ]);
        $products   = $serializer->serialize($products, 'json', ['groups' => BiProduct::PRODUCT_READ]);
        $customers  = $serializer->serialize($customers, 'json', ['groups' => BiCustomer::CUSTOMER_READ]);
        $sites      = $serializer->serialize($sites, 'json', ['groups' => BiSite::SITE_READ]);

        [$taxes, $unities] = $this->billService->getTaxesAndUnitiesData($user->getNameManager(), $society, true);

        return $this->render('user/pages/bill/contract.html.twig', [
            'pageName' => "contrats",
            'donnees' => $objs,
            'relations' => $relations,
            'items' => $items,
            'products' => $products,
            'customers' => $customers,
            'society' => $society,
            'taxes' => $taxes,
            'unities' => $unities,
            'sites' => $sites,
        ]);
    }

    /**
     * @Route("/contrats/traitement/{year}/{month}", options={"expose"=true}, name="contracts_process")
     */
    public function contractsProcess($year, $month, SerializerInterface $serializer, ContractService $contractService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society    = $this->billService->getSociety($user);
        $objs       = $em->getRepository(BiContract::class)->findBy(['society' => $society]);
        $objs       = $contractService->getData($month, $objs);
        $relations  = $em->getRepository(BiContractCustomer::class)->findByContractsAndNumeroNotNull($objs);
        $invoices   = $em->getRepository(BiInvoice::class)->findWithContractBySociety($society->getId());

        $data = [];
        /** @var BiContract $obj */
        foreach($objs as $obj){
            $find = false;
            foreach($relations as $relation){
                if($relation->getContract()->getId() == $obj->getId()){
                    $find = true;
                }
            }

            if($find){
                $data[] = $obj;
            }
        }

        $objs       = $serializer->serialize($data, 'json', ['groups' => BiContract::CONTRACT_READ]);
        $relations  = $serializer->serialize($relations, 'json', ['groups' => BiContractCustomer::RELATION_READ]);
        $society    = $serializer->serialize($society, 'json', ['groups' => BiSociety::BILL_READ]);
        $invoices   = $serializer->serialize($invoices, 'json', ['groups' => BiInvoice::CONTRACT_READ]);

        return $this->render('user/pages/bill/contract_process.html.twig', [
            'pageName' => "traitement",
            'year' => $year,
            'month' => $month,
            'donnees' => $objs,
            'relations' => $relations,
            'invoices' => $invoices,
            'society' => $society,
        ]);
    }

    /**
     * @Route("/factures/export/comptabilite", name="export_compta", methods={"GET"})
     */
    public function exportCompta()
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->billService->getEntityManager($user);

        $society = $this->billService->getSociety($user);

        $objs = $em->getRepository(BiInvoice::class)->findBy(['society' => $society, 'isExported' => false], ['dateAt' => 'DESC']);
        $products   = $em->getRepository(BiProduct::class)->findBy(['society' => $society]);
        $taxes      = $em->getRepository(BiTaxe::class)->findBy(['society' => [null, $society]]);

        $filename = $this->billService->getExportCompta($objs, $society, $products, $taxes);
        if(!$filename){
            $this->addFlash('error', 'Il n\'y a pas de données à exporter.');
            return $this->redirectToRoute('user_settings_compta_index');
        }

        $em->flush();

        $file = $this->getParameter('private_directory').'/bill/export/'. $filename;
        if(!file_exists($file)){
            throw new NotFoundException("Fichier introuvable.");
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');

        return new BinaryFileResponse($file);
    }
}
