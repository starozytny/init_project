<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImAgency;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
use App\Service\Immo\ImmoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/immo/agency", name="api_immo_agency_")
 */
class AgencyController extends AbstractController
{
    /**
     * Admin - Delete an agency
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Agency")
     *
     * @param ApiResponse $apiResponse
     * @param ImAgency $obj
     * @param ImmoService $immoService
     * @return JsonResponse
     */
    public function delete(ApiResponse $apiResponse, ImAgency $obj, ImmoService $immoService): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $immoService->deleteAgency($obj);

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("Supression réussie !");
    }
}
