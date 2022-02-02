<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImBien;
use App\Entity\Immo\ImBuyer;
use App\Entity\Immo\ImProspect;
use App\Entity\Immo\ImSearch;
use App\Entity\Immo\ImSuivi;
use App\Entity\User;
use App\Repository\Immo\ImBuyerRepository;
use App\Repository\Immo\ImProspectRepository;
use App\Service\ApiResponse;
use App\Service\Data\DataImmo;
use App\Service\Data\DataService;
use App\Service\Immo\SearchService;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/searchs", name="api_searchs_")
 */
class SearchController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @throws Exception
     */
    public function submitForm($type, ImSearch $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataImmo $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setDataSearch($obj, $data);

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Create a search
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message"
     * )
     *
     * @OA\Tag(name="Searchs")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @param ValidatorService $validator
     * @param DataImmo $dataEntity
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request, ApiResponse $apiResponse, ValidatorService $validator, DataImmo $dataEntity): JsonResponse
    {
        return $this->submitForm("create", new ImSearch(), $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * Update a search
     *
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message"
     * )
     *
     * @OA\Tag(name="Searchs")
     *
     * @param ImSearch $obj
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @param ValidatorService $validator
     * @param DataImmo $dataEntity
     * @return JsonResponse
     * @throws Exception
     */
    public function update(ImSearch $obj, Request $request, ApiResponse $apiResponse, ValidatorService $validator, DataImmo $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * Delete a search
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message"
     * )
     *
     * @OA\Tag(name="Searchs")
     *
     * @param ImSearch $obj
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function delete(ImSearch $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Delete a group of searchs
     *
     * @Route("/", name="delete_group", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Searchs")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(ImSearch::class, json_decode($request->getContent()));
    }

    /**
     * Get results search
     *
     * @Route("/results/{id}", name="results", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Searchs")
     *
     * @param Request $request
     * @param ImSearch $search
     * @param ApiResponse $apiResponse
     * @param SearchService $searchService
     * @return JsonResponse
     */
    public function results(Request $request, ImSearch $search, ApiResponse $apiResponse, SearchService $searchService): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $biens = $em->getRepository(ImBien::class)->findByCodeAdBienWithoutArchive(
            $search->getCodeTypeAd(), $search->getCodeTypeBien()
        );

        $data = json_decode($request->getContent());

        $biens = $searchService->filterLocalisation('zipcode', $biens, $search->getZipcode());
        $biens = $searchService->filterLocalisation('city',    $biens, $search->getCity());

        $biens = $searchService->filterAdvantage('lift',    $biens, $search->getHasLift());
        $biens = $searchService->filterAdvantage('terrace', $biens, $search->getHasTerrace());
        $biens = $searchService->filterAdvantage('balcony', $biens, $search->getHasBalcony());
        $biens = $searchService->filterAdvantage('parking', $biens, $search->getHasParking());
        $biens = $searchService->filterAdvantage('box',     $biens, $search->getHasBox());

        $biens = $searchService->filterMinMax('price', $biens, $search->getMinPrice(), $search->getMaxPrice(), $data->price ?? 0);
        $biens = $searchService->filterMinMax('piece', $biens, $search->getMinPiece(), $search->getMaxPiece(), $data->piece ?? 0);
        $biens = $searchService->filterMinMax('room',  $biens, $search->getMinRoom(),  $search->getMaxRoom(), $data->room ?? 0);
        $biens = $searchService->filterMinMax('area',  $biens, $search->getMinArea(),  $search->getMaxArea(), $data->area ?? 0);
        $biens = $searchService->filterMinMax('land',  $biens, $search->getMinLand(),  $search->getMaxLand(), $data->land ?? 0);

        return $apiResponse->apiJsonResponse($biens, User::USER_READ);
    }
}
