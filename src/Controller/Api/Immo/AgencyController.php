<?php

namespace App\Controller\Api\Immo;

use App\Entity\Immo\ImAgency;
use App\Entity\User;
use App\Manager\CreateAgency;
use App\Service\ApiResponse;
use App\Service\Export;
use App\Service\FileUploader;
use App\Service\Immo\ImmoService;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/immo/agency", name="api_immo_agency_")
 */
class AgencyController extends AbstractController
{
    const FOLDER_LOGO = "immo/logos";
    const FOLDER_TARIF = "immo/tarifs";

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Admin - Create an agency
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new user object"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Agency")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param FileUploader $fileUploader
     * @param CreateAgency $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, ApiResponse $apiResponse,
                           FileUploader $fileUploader, CreateAgency $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->get('data'));

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setData(new ImAgency(), $data);

        $file = $request->files->get('logo');
        if ($file) {
            $fileName = $fileUploader->upload($file, self::FOLDER_LOGO);
            $obj->setLogo($fileName);
        }

        $file = $request->files->get('tarif');
        if ($file) {
            $fileName = $fileUploader->upload($file, self::FOLDER_TARIF);
            $obj->setTarif($fileName);
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Update an agency
     *
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an user object"
     * )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden for not good role or user",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Agency")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param ImAgency $obj
     * @param FileUploader $fileUploader
     * @param CreateAgency $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorService $validator, ApiResponse $apiResponse, ImAgency $obj,
                           FileUploader $fileUploader, CreateAgency $dataEntity): JsonResponse
    {
        if ($this->getUser() != $obj && !$this->isGranted("ROLE_ADMIN")) {
            return $apiResponse->apiJsonResponseForbidden();
        }

        $em = $this->doctrine->getManager();
        $data = json_decode($request->get('data'));

        if($data === null){
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setData($obj, $data);

        $file = $request->files->get('logo');
        if ($file) {
            $fileName = $fileUploader->replaceFile($file, $obj->getLogo(),self::FOLDER_LOGO);
            $obj->setLogo($fileName);
        }

        $file = $request->files->get('tarif');
        if ($file) {
            $fileName = $fileUploader->replaceFile($file, $obj->getTarif(),self::FOLDER_TARIF);
            $obj->setTarif($fileName);
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

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
        $em = $this->doctrine->getManager();

        $immoService->deleteAgency($obj);

        $em->flush();
        return $apiResponse->apiJsonResponseSuccessful("Supression réussie !");
    }

    /**
     * Export list agencies
     *
     * @Route("/export/{format}", name="export", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new agency object",
     * )
     *
     * @OA\Tag(name="Agency")
     *
     * @param Export $export
     * @param $format
     * @return BinaryFileResponse
     */
    public function export(Export $export, $format): BinaryFileResponse
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository(ImAgency::class)->findAll();
        $data = [];

        $nameFile = 'agences';
        $nameFolder = 'export/';

        foreach ($objs as $obj) {
            $tmp = [
                $obj->getName(),
                $obj->getDirname(),
                $obj->getPhoneLocation(),
                $obj->getPhoneVente(),
                $obj->getEmail(),
                $obj->getWebsite(),
                $obj->getLogo(),
                $obj->getAddress(),
                $obj->getZipcode(),
                $obj->getCity(),
                "",
                $obj->getLat(),
                $obj->getLon(),
                "", "", "", "",
                $obj->getTarif(),
                $obj->getDescription(),
                $obj->getPhone(),
                $obj->getLegal(),
                $obj->getEmailLocation(),
                $obj->getEmailVente()
            ];
            if(!in_array($tmp, $data)){
                array_push($data, $tmp);
            }
        }

        $fileName = $nameFile . '.xlsx';
        $header = [['Nom', 'Dirname', 'Téléphone Location', 'Téléphone Vente', 'Adresse e-mail standard', 'Adresse URL',
            'Logo', 'Adresse', 'Code postal', 'Ville', 'Arrondissement', 'Latitude', 'Longitude',
            'Catégorie - locative', 'Catégorie - vente', 'Catégorie - syndic', 'Catégorie - gérance',
            'Tarif', 'Description', 'Téléphone standard', 'Legal', 'Adresse e-mail location', 'Adresse e-mail vente'
        ]];

        $export->createFile($format, 'Liste des ' . $nameFile, $fileName , $header, $data, 23, $nameFolder);
        return new BinaryFileResponse($this->getParameter('private_directory'). $nameFolder . $fileName);
    }
}
