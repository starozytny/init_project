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
 * @Route("/api/immo/ads", name="api_immo_ads_")
 */
class AdsController extends AbstractController
{
    /**
     * Get ads data
     *
     * @Route("/", name="read", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns ads list objects",
     * )
     * @OA\Tag(name="Ads")
     *
     * @param ApiResponse $apiResponse
     * @param ImBienRepository $repository
     * @return JsonResponse
     */
    public function read(ApiResponse $apiResponse, ImBienRepository $repository): JsonResponse
    {
        $ads = $repository->findAll();
        return $apiResponse->apiJsonResponse($ads, ImBien::LIST_READ);
    }

    /**
     * Search ads data
     *
     * @Route("/search", name="search", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns ads list objects",
     * )
     * @OA\Tag(name="Ads")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @param ImBienRepository $repository
     * @return JsonResponse
     */
    public function search(Request $request, ApiResponse $apiResponse, ImBienRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent());
        $ads = $repository->findAll();
        return $apiResponse->apiJsonResponse($ads, ImBien::LIST_READ);
    }

    /**
     * Get pdf ad
     *
     * @Route("/impression/{identifiant}", name="print", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns ads list objects",
     * )
     * @OA\Tag(name="Ads")
     *
     * @param ApiResponse $apiResponse
     * @param $identifiant
     * @param ImBienRepository $repository
     * @return JsonResponse
     * @throws MpdfException
     */
    public function print(ApiResponse $apiResponse, $identifiant, ImBienRepository $repository): JsonResponse
    {
        $ad = $repository->findOneBy(['identifiant' => $identifiant]);
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

        $mpdf->Output('impression-'.$ad->getIdentifiant().'.pdf', Destination::INLINE);
        return new JsonResponse(1);
    }
}
