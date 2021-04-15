<?php

namespace App\Controller\Api;

use App\Entity\Immo\ImBien;
use App\Repository\Immo\ImBienRepository;
use App\Service\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="index", options={"expose"=true}, methods={"GET"})
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
    public function index(ApiResponse $apiResponse, ImBienRepository $repository): JsonResponse
    {
        $ads = $repository->findAll();
        return $apiResponse->apiJsonResponse($ads, ImBien::ADMIN_LIST_READ);
    }
}
