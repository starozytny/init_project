<?php

namespace App\Service\Bill;

use App\Entity\Society;
use App\Entity\User;
use App\Entity\Bill\BiAvoir;
use App\Entity\Bill\BiContract;
use App\Entity\Bill\BiContractCustomer;
use App\Entity\Bill\BiCustomer;
use App\Entity\Bill\BiHistory;
use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiItem;
use App\Entity\Bill\BiProduct;
use App\Entity\Bill\BiQuotation;
use App\Entity\Bill\BiSite;
use App\Entity\Bill\BiTaxe;
use App\Entity\Bill\BiUnity;
use App\Entity\Bill\BiSociety;
use App\Service\ApiResponse;
use App\Service\Data\Bill\DataBill;
use App\Service\Export;
use App\Service\FileCreator;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class BillService
{
    private $serializer;
    private $fileCreator;
    private $publicDirectory;
    private $export;
    private $em;

    public function __construct($publicDirectory, EntityManagerInterface $entityManager, SerializerInterface $serializer,
                                FileCreator $fileCreator, Export $export)
    {
        $this->publicDirectory = $publicDirectory;
        $this->serializer = $serializer;
        $this->fileCreator = $fileCreator;
        $this->export = $export;
        $this->em = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getPublicDirectory()
    {
        return $this->publicDirectory;
    }

    public function getMainSociety($id)
    {
        $society = $this->em->getRepository(Society::class)->find($id);

        $this->em->refresh($society);
        return $society;
    }

    public function getSociety($user)
    {
        return $this->em->getRepository(BiSociety::class)->findOneBy(['code' => $user->getSociety()->getCode()]);
    }

    public function getSocietyByMainSociety(Society $society)
    {
        return $this->em->getRepository(BiSociety::class)->findOneBy(['code' => $society->getCode()]);
    }

    public function getLogoSociety(BiSociety $society): string
    {
        if($society->getLogo()){
            $file = $this->getPublicDirectory() . BiSociety::FOLDER_LOGOS . '/' . $society->getLogo();

            if(file_exists($file)){
                $data = file_get_contents($file);
                $extension = pathinfo($file, PATHINFO_EXTENSION);;

                return 'data:image/' . $extension . ';base64,' . base64_encode($data);
            }
        }

        return "data:image/png;base64,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";
    }

    public function createNewNumero($i, $year, $prefix): string
    {
        $tab = array_map('intval', str_split($i));
        $nbZero = 6 - count($tab);

        $counter = $year . "-" . str_repeat("0", $nbZero);
        $counter .= $i;

        return $prefix . $counter;
    }

    public function getTaxesAndUnitiesData(BiSociety $society, $withSerializer = false): array
    {
        $taxes = $this->em->getRepository(BiTaxe::class)->findBy(['society' => [null, $society]]);
        $unities = $this->em->getRepository(BiUnity::class)->findBy(['society' => [null, $society]]);

        if($withSerializer) {
            $taxes = $this->serializer->serialize($taxes, 'json', ['groups' => User::USER_READ]);
            $unities = $this->serializer->serialize($unities, 'json', ['groups' => User::USER_READ]);
        }

        return [$taxes, $unities];
    }

    /**
     * @param $user
     * @param BiInvoice[] $data
     * @param string $destination
     * @return Response
     * @throws MpdfException
     */
    public function getInvoice($user, array $data, $destination = Destination::INLINE): Response
    {
        $name = count($data) == 1 ? ($data[0]->getStatus() == BiInvoice::STATUS_DRAFT ? "Brouillon" : $data[0]->getNumero()) : "Factures";
        $title = (count($data) == 1 ? 'Facture N°' : "");
        $filename = 'facture-'.$name.'.pdf';

        return $this->getFile($user, "user/pdf/bill/invoice.html.twig", $data, $title, $name, $filename, $destination);
    }

    /**
     * @param $user
     * @param BiQuotation[] $data
     * @param string $destination
     * @return Response
     * @throws MpdfException
     */
    public function getQuotation($user, array $data, $destination = Destination::INLINE): Response
    {
        $name = count($data) == 1 ? ($data[0]->getStatus() == BiQuotation::STATUS_DRAFT ? "Brouillon" : $data[0]->getNumero()) : "Devis";
        $title = (count($data) == 1 ? 'Devis N°' : "");
        $filename = 'devis-'.$name.'.pdf';

        return $this->getFile($user,"user/pdf/bill/quotation.html.twig", $data, $title, $name, $filename, $destination);
    }

    /**
     * @param $user
     * @param BiAvoir[] $data
     * @param string $destination
     * @return Response
     * @throws MpdfException
     */
    public function getAvoir($user, array $data, $destination = Destination::INLINE): Response
    {
        $name = count($data) == 1 ? ($data[0]->getStatus() == BiAvoir::STATUS_DRAFT ? "Brouillon" : $data[0]->getNumero()) : "Avoirs";
        $title = (count($data) == 1 ? 'Avoir N°' : "");
        $filename = 'avoir-'.$name.'.pdf';

        return $this->getFile($user,"user/pdf/bill/avoir.html.twig", $data, $title, $name, $filename, $destination);
    }

    /**
     * @param $user
     * @param $template
     * @param BiInvoice[]|BiQuotation[]|BiAvoir[] $data
     * @param $title
     * @param $name
     * @param $filename
     * @param $destination
     * @return Response
     * @throws MpdfException
     * @throws Exception
     */
    private function getFile($user, $template, array $data, $title, $name, $filename, $destination): Response
    {
        $mpdf = $this->fileCreator->initPDF($title . $name, 'Pz3zGgit7hy5');
        $mpdf = $this->fileCreator->addCustomStyle($mpdf, 'custom-bill.css');

        $haveDraft = false;

        $i = 0;
        $tvas = [];
        foreach($data as $elem){
            if ($i != 0) {
                $mpdf->AddPage();
            }
            $i++;

            if($elem->getStatus() == BiInvoice::STATUS_DRAFT){
                $haveDraft = true;
            }

            $products = $this->em->getRepository(BiProduct::class)->findBy(['identifiant' => $elem->getIdentifiant()]);

            foreach($products as $product){
                $quantity = $product->getQuantity();
                $codeTva = $product->getCodeTva();
                $price = $product->getPrice();

                $montantHt = $quantity * $price;
                $total = $montantHt * ($product->getRateTva() / 100);

                if(isset($tvas[$codeTva])){
                    $tvas[$codeTva]["base"] += $montantHt;
                    $tvas[$codeTva]["total"] += $total;
                }else{
                    $tvas[$codeTva] = [
                        "code" => $codeTva,
                        "base" => round($montantHt,2),
                        "rate" => $product->getRateTva(),
                        "total" => round($total,2)
                    ];
                }
            }

            $mpdf = $this->fileCreator->writePDF($mpdf, $template, [
                'elem' => $elem,
                'products' => $products,
                'tvas' => $tvas
            ]);
        }

        if($haveDraft){
            $mpdf->SetWatermarkText('Brouillon');
            $mpdf->showWatermarkText = true;
        }

        $mpdf = $this->fileCreator->outputPDF($mpdf, $filename, $destination);

        return new Response(1);
    }

    /**
     * @param $user
     * @param BiInvoice|BiQuotation|BiContract|BiAvoir $obj
     * @return void
     */
    public function removeProducts($user, $obj)
    {
        $products = $this->em->getRepository(BiProduct::class)->findBy(['identifiant' => $obj->getIdentifiant()]);
        if(count($products) != 0){
            foreach($products as $pr){
                $this->em->remove($pr);
            }
        }
    }

    /**
     * @param $user
     * @param BiInvoice|BiQuotation|BiContract|BiAvoir $obj
     * @param BiProduct[] $products
     * @param $type
     * @return void
     */
    public function updateProductIdentifiant($user, $obj, array $products, $type)
    {
        foreach($products as $product){
            $product = ($product)
                ->setType($type)
                ->setIdentifiant($obj->getIdentifiant())
            ;

            $this->em->persist($product);
        }
    }

    /**
     * @throws Exception
     */
    public function generateInvoice(BiInvoice $obj, $data, $user, DataBill $dataBill,
                                    $year = null, $month = null, $nRelations = null): BiInvoice
    {
        $obj = $dataBill->setDataInvoiceGenerated($obj, $data);

        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone("Europe/Paris"));

        $history = $dataBill->setDataHistory(new BiHistory(), $obj, BiHistory::TYPE_GENERATED, "Facture finalisée", $now);

        $obj = ($obj)
            ->setIsHidden(false)
            ->setIsSent(true)
        ;

        if($obj->getQuotationId()){
            $quotation = $this->em->getRepository(BiQuotation::class)->find($obj->getQuotationId());
            if($quotation){
                ($quotation)
                    ->setRefInvoice($obj->getNumero())
                ;
            }
        }

        if($obj->getRelationId() && $year && $month){
            $relation = null;

            if($nRelations){
                /** @var BiContractCustomer $rel */
                foreach($nRelations as $rel){
                    if($rel->getId() == $obj->getRelationId()){
                        $relation = $rel;
                    }
                }
            }else{
                if($rel = $this->em->getRepository(BiContractCustomer::class)->find($obj->getRelationId())){
                    $relation = $rel;
                }
            }

            if($relation){
                ($relation)
                    ->setLastMonth($year)
                    ->setLastMonth($month)
                ;
            }
        }

        $this->em->persist($history);

        return $obj;
    }

    /**
     * @throws Exception
     */
    public function generateAndSendInvoice(BiInvoice $obj, $data, $user, DataBill $dataBill,
                                           ApiResponse $apiResponse, MailerService $mailerService,
                                           $year = null, $month = null): JsonResponse
    {
        if($obj->getStatus() !== BiInvoice::STATUS_DRAFT){
            return $apiResponse->apiJsonResponseBadRequest("Vous ne pouvez pas générer une facture établie.");
        }

        $invoice = $this->generateInvoice($obj, $data, $user, $dataBill, $year, $month);
        $this->em->flush();

        if(!$mailerService->sendInvoice($invoice)){
            $invoice->setIsSent(false);
            $this->em->flush();

            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez contacter le support."
            ]]);
        }

        return $apiResponse->apiJsonResponse($invoice, BiInvoice::INVOICE_READ);
    }

    public function getDataCommonPage(BiSociety $society, $typeProducts, SerializerInterface $serializer): array
    {
        $products  = $this->em->getRepository(BiProduct::class)->findBy(['society' => $society, 'type' => $typeProducts]);
        $items     = $this->em->getRepository(BiItem::class)->findBy(['society' => $society]);
        $customers = $this->em->getRepository(BiCustomer::class)->findBy(['society' => $society]);
        $sites     = $this->em->getRepository(BiSite::class)->findBy(['customer' => $customers]);

        [$taxes, $unities] = $this->getTaxesAndUnitiesData($society, true);

        $society   = $serializer->serialize($society, 'json', ['groups' => User::ADMIN_READ]);
        $products  = $serializer->serialize($products, 'json', ['groups' => BiProduct::PRODUCT_READ]);
        $items     = $serializer->serialize($items, 'json', ['groups' => BiItem::ITEM_READ]);
        $customers = $serializer->serialize($customers, 'json', ['groups' => BiCustomer::CUSTOMER_READ]);
        $sites     = $serializer->serialize($sites, 'json', ['groups' => BiSite::SITE_READ]);

        return [
            'society' => $society,
            'items' => $items,
            'taxes' => $taxes,
            'unities' => $unities,
            'products' => $products,
            'customers' => $customers,
            'sites' => $sites,
        ];
    }

    /**
     * @param BiInvoice[] $invoices
     * @param BiSociety $society
     * @param BiProduct[] $products
     * @param BiTaxe[] $taxes
     * @return false|string
     */
    public function getExportCompta(array $invoices, BiSociety $society, array $products, array $taxes)
    {
        $limit = new \DateTime();
        $limit->setTimezone(new \DateTimeZone("Europe/Paris"));

        $startAt = $society->getDateCompta();

        $data = [];
        foreach($invoices as $invoice){
            if(!$invoice->getIsExported() && $invoice->getStatus() != BiInvoice::STATUS_DRAFT){
                if($invoice->getDateAt() <= $limit){
                    if(($startAt && $invoice->getDateAt() >= $startAt) || $startAt == null){
                        $data = $this->setDataExportCompta($data, $invoice, $products, $taxes);
                        $invoice->setIsExported(true);
                    }
                }
            }
        }

        if(count($data) == 0){
            return false;
        }

        $header = [[
            'Date',
            'Code journal',
            'Numéro de compte',
            'Numéro de facture',
            'Nom client',
            'Débit',
            'Crédit'
        ]];

        $society->setDateCompta($limit);

        $filename = 'comptabilite-' . $limit->format('y-m-d-H-i') . ".csv";
        $this->export->createFile('csv', 'Comptabilite', $filename, $header, $data, 7, "bill/export/");

        return $filename;
    }

    /**
     * @param array $data
     * @param BiInvoice $obj
     * @param BiProduct[] $products
     * @param BiTaxe[] $taxes
     * @return array
     */
    public function setDataExportCompta(array $data, BiInvoice $obj, array $products, array $taxes): array
    {
        $dateAt = $obj->getDateAt()->format("d/m/Y");
        $name   = $obj->getToName();
        $numero = $obj->getNumero();

        $data[] = [
            $dateAt,
            "VT",
            $obj->getRefCustomer() ?: $obj->getToName(),
            $numero,
            $name,
            $obj->getTotalTtc(),
            0,
        ];

        foreach($products as $product){
            if($product->getIdentifiant() == $obj->getIdentifiant()){
                if($product->getQuantity() && $product->getQuantity() != 0 && $product->getRateTva() != 0){
                    $totalHt = $product->getQuantity() * $product->getPrice();

                    $data[] = [
                        $dateAt,
                        "VT",
                        $product->getNumero() ?: $product->getId(),
                        $numero,
                        $name,
                        0,
                        $totalHt,
                    ];

                    $numTva = 445710;
                    foreach ($taxes as $tax) {
                        if($tax->getCode() == $product->getCodeTva()){
                            $numTva = $tax->getNumeroComptable();
                        }
                    }

                    $data[] = [
                        $dateAt,
                        "VT",
                        $numTva,
                        $numero,
                        $name,
                        0,
                        $totalHt * ($product->getRateTva() / 100),
                    ];
                }
            }
        }

        return $data;
    }
}
