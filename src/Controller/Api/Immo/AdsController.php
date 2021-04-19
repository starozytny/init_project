<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImBien;
use App\Repository\Immo\ImBienRepository;
use App\Service\ApiResponse;
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
}
