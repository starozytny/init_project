<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImBien;
use App\Repository\Immo\ImBienRepository;
use App\Service\ApiResponse;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/immo/ad", name="api_immo_ad_")
 */
class AdController extends AbstractController
{
    /**
     * Get ad data
     *
     * @Route("/{slug}", name="read", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns ad objects",
     * )
     * @OA\Tag(name="Ad")
     *
     * @param ApiResponse $apiResponse
     * @param $slug
     * @param ImBienRepository $repository
     * @return JsonResponse
     */
    public function read(ApiResponse $apiResponse, $slug, ImBienRepository $repository): JsonResponse
    {
        $ad = $repository->findOneBy(['slug' => $slug]);
        return $apiResponse->apiJsonResponse($ad, ImBien::SHOW_READ);
    }

    /**
     * Get pdf ad
     *
     * @Route("/impression/{slug}", name="print", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns ads list objects",
     * )
     * @OA\Tag(name="Ad")
     *
     * @param ApiResponse $apiResponse
     * @param $slug
     * @param ImBienRepository $repository
     * @return JsonResponse
     * @throws MpdfException
     */
    public function print(ApiResponse $apiResponse, $slug, ImBienRepository $repository): JsonResponse
    {
        $ad = $repository->findOneBy(['slug' => $slug]);
        if(!$ad){
            return $apiResponse->apiJsonResponseBadRequest("Ce lien est invalide.");
        }

        $mpdf = new Mpdf();
        $title = $ad->getAgency()->getName() . " - " . $ad->getTypeAd() . " - " . $ad->getLabel() . ' - ' . $ad->getAddress()->getZipcode() . ", " . $ad->getAddress()->getCity();
        $mpdf->SetTitle($title);
        $mpdf->SetHeader($title);
        $mpdf->SetFooter("Ref : " . $ad->getRealRef() ?? $ad->getRef());
        $stylesheet = file_get_contents($this->getParameter('public_directory') . '/pdf/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents($this->getParameter('public_directory') . '/pdf/css/custom-pdf.css');
        $mpdf->WriteHTML($stylesheet,HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($stylesheet2,HTMLParserMode::HEADER_CSS);
        $mpdf->SetProtection(array(
            'print'
        ),'', 'nhc6NyMdASq8');

        $file = $this->getParameter('kernel.project_dir') . '/public/annonces/images/' . $ad->getAgency()->getDirname() . '/' . $ad->getFirstImage();
        $mpdf->WriteHTML(
            $this->renderView('app/pdf/ad.html.twig', ['ad' => $ad, 'image' => $file]),
            HTMLParserMode::HTML_BODY
        );

        $mpdf->Output('impression-'.$ad->getSlug().'.pdf', Destination::INLINE);
        return new JsonResponse(1);
    }
}
