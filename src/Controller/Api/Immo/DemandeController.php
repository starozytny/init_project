<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImBien;
use App\Entity\Immo\ImDemande;
use App\Service\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/immo/demande", name="api_immo_demande_")
 */
class DemandeController extends AbstractController
{
    /**
     * Create demandes data
     *
     * @Route("/", name="create", methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns demande object",
     * )
     * @OA\Tag(name="Ads")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function create(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        if(!$data){
            return $apiResponse->apiJsonResponseBadRequest("Il manque des données.");
        }

        if(!$data->name || !$data->phone || !$data->message || !$data->bien){
            return $apiResponse->apiJsonResponseBadRequest("Il manque des données.");
        }

        $bien = $em->getRepository(ImBien::class)->find($data->bien);
        if(!$bien){
            return $apiResponse->apiJsonResponseBadRequest("Ce bien immobilier n'existe pas.");
        }

        $demande = (new ImDemande())
            ->setName($data->name)
            ->setPhone($data->phone)
            ->setMessage($data->message)
            ->setBien($bien)
            ->setBienIdentifiant($bien->getIdentifiant())
        ;

        $em->persist($demande);
        $em->flush();

        return $apiResponse->apiJsonResponse("Demande envoyée.");
    }
}
