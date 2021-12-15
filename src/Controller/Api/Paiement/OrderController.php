<?php

namespace App\Controller\Api\Paiement;

use App\Entity\Paiement\PaBank;
use App\Entity\Paiement\PaOrder;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
use App\Service\Data\Paiement\DataBank;
use App\Service\ValidatorService;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/orders", name="api_orders_")
 */
class OrderController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Developer - Delete a order
     *
     * @Security("is_granted('ROLE_DEVELOPER')")
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Orders")
     *
     * @param PaOrder $obj
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function delete(PaOrder $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Developer - Delete a group of order
     *
     * @Security("is_granted('ROLE_DEVELOPER')")
     *
     * @Route("/", name="delete_group", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Orders")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(PaOrder::class, json_decode($request->getContent()));
    }
}
