<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImBien;
use App\Entity\Immo\ImDemande;
use App\Repository\Immo\ImBienRepository;
use App\Repository\Immo\ImDemandeRepository;
use App\Service\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

        return $apiResponse->apiJsonResponse("ok");
    }
    /**
     * Get demandes data
     *
     * @Security("is_granted('ROLE_ADMIN')")
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
     * @param ImDemandeRepository $repository
     * @return JsonResponse
     */
    public function read(ApiResponse $apiResponse, ImDemandeRepository $repository): JsonResponse
    {
        $demandes = $repository->findAll();
        return $apiResponse->apiJsonResponse($demandes);
    }
}
