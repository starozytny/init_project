<?php

namespace App\Controller\Api\Bill;

use App\Entity\Society;
use App\Entity\User;
use App\Entity\Bill\BiSociety;
use App\Service\ApiResponse;
use App\Service\Bill\BillService;
use Doctrine\Common\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/bill/general", name="api_bill_general_")
 */
class GeneralController extends AbstractController
{
    private $billService;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, BillService $billService)
    {
        $this->billService = $billService;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/data/{id}", name="data", options={"expose"=true}, methods={"GET"})
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
     * @param $id
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function data($id, ApiResponse $apiResponse): JsonResponse
    {
        $mainSociety = $this->billService->getMainSociety($id);

        $society = $this->billService->getSocietyByMainSociety($mainSociety);

        return $apiResponse->apiJsonResponse($society, User::ADMIN_READ);
    }

    /**
     * @Route("/generate/{id}", name="generate_society", options={"expose"=true}, methods={"GET"})
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
     * @param Society $obj
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function generateSociety(Society $obj, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $society = $this->billService->getSocietyByMainSociety($obj);

        if(!$society){
            $society = (new BiSociety())
                ->setCode($obj->getCode())
                ->setName($obj->getName())
            ;

            $em->persist($society);
            $em->flush();
        }

        return $apiResponse->apiJsonResponse($society, User::ADMIN_READ);
    }
}
