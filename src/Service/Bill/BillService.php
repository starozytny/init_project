<?php

namespace App\Service\Bill;

use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiProduct;
use App\Entity\Bill\BiTaxe;
use App\Entity\Bill\BiUnity;
use App\Entity\Society;
use App\Entity\User;
use App\Service\FileCreator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class BillService
{
    private $em;
    private $serializer;
    private $fileCreator;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, FileCreator $fileCreator)
    {
        $this->em = $entityManager;
        $this->serializer = $serializer;
        $this->fileCreator = $fileCreator;
    }

    public function createNewNumero($i, $year, $prefix): string
    {
        $tab = array_map('intval', str_split($i));
        $nbZero = 6 - count($tab);

        $counter = $year . "-" . str_repeat("0", $nbZero);
        $counter .= $i;

        return $prefix . $counter;
    }

    public function getTaxesAndUnitiesData(Society $society, $withSerializer = false): array
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
     * @param BiInvoice[] $data
     * @param $destination
     * @return Response
     * @throws MpdfException
     */
    public function getInvoice(array $data, $destination = Destination::INLINE): Response
    {
        $name = count($data) == 1 ? $data[0]->getNumero() : "Factures";
        $title = (count($data) == 1 ? 'Facture N°' : "");
        $filename = 'facture-'.$name.'.pdf';

        return $this->getFile("user/pdf/bill/invoice.html.twig", $data, $title, $name, $filename, $destination);
    }

    /**
     * @param $template
     * @param BiInvoice[] $data
     * @param $title
     * @param $name
     * @param $filename
     * @param $destination
     * @return Response
     * @throws MpdfException
     * @throws Exception
     */
    private function getFile($template, array $data, $title, $name, $filename, $destination): Response
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

                if($quantity && $quantity != "" && $price && $price != "" && $codeTva != 0){
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

}
