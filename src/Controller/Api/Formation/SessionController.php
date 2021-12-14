<?php

namespace App\Controller\Api\Formation;

use App\Entity\Formation\FoFormation;
use App\Entity\Formation\FoRegistration;
use App\Entity\Formation\FoSession;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\DataFormation;
use App\Service\Data\DataService;
use App\Service\FileCreator;
use App\Service\ValidatorService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/sessions", name="api_sessions_")
 */
class SessionController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function submitForm($type, FoFormation $formation, FoSession $obj, Request $request, ApiResponse $apiResponse,
                               ValidatorService $validator, DataFormation $dataEntity): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setDataSession($formation, $obj, $data);

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $em->persist($obj);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Create a session
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/{formation}", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Formations")
     *
     * @param Request $request
     * @param FoFormation $formation
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataFormation $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, FoFormation $formation, ValidatorService $validator, ApiResponse $apiResponse,
                           DataFormation $dataEntity): JsonResponse
    {
        return $this->submitForm("create", $formation, new FoSession(), $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * Update a session
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/{formation}/{id}", name="update", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an session object"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Formations")
     *
     * @param Request $request
     * @param FoFormation $formation
     * @param FoSession $obj
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataFormation $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, FoFormation $formation, FoSession $obj, ValidatorService $validator,
                           ApiResponse $apiResponse, DataFormation $dataEntity): JsonResponse
    {
        return $this->submitForm("update", $formation, $obj, $request, $apiResponse, $validator, $dataEntity);
    }

    /**
     * Switch is published
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/session/{id}", name="formation_published", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a session object",
     * )
     *
     * @OA\Tag(name="Formations")
     *
     * @param DataService $dataService
     * @param FoSession $obj
     * @return JsonResponse
     */
    public function switchIsPublished(DataService $dataService, FoSession $obj): JsonResponse
    {
        return $dataService->switchIsPublished($obj);
    }

    /**
     * Admin - Delete a session
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
     * @OA\Tag(name="Formations")
     *
     * @param FoSession $obj
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function delete(FoSession $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * Admin - Delete a group of sessions
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", name="delete_group", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Formations")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteSelected(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(FoSession::class, json_decode($request->getContent()));
    }

    /**
     * Generate convention
     *
     * @Route("/conventions/{slug}", name="conventions", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param FoSession $session
     * @param FileCreator $fileCreator
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     * @throws MpdfException
     * @throws Exception
     */
    public function convention(FoSession $session, FileCreator $fileCreator, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        /** @var User $user */
        $user = $this->getUser();
        $registrations = $em->getRepository(FoRegistration::class)->findBy(['session' => $session, 'user' => $user]);
        $workers = [];
        foreach($registrations as $registration){
            $workers[] = $registration->getWorker();
        }

        $mpdf = $fileCreator->initPDF("Conventions");
        $mpdf = $fileCreator->addCustomStyle($mpdf, "convention.css");

        $mpdf = $fileCreator->writePDF($mpdf, "user/pdf/convention.html.twig", [
            'formation' => $session->getFormation(),
            'session' => $session,
            'workers' => $workers,
            'user' => $user
        ]);

        $mpdf = $fileCreator->outputPDF($mpdf, 'conventions.pdf');

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }

    /**
     * Generate attestation
     *
     * @Route("/attestations/{slug}", name="attestations", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param FoSession $session
     * @param FileCreator $fileCreator
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     * @throws MpdfException
     * @throws Exception
     */
    public function attestations(FoSession $session, FileCreator $fileCreator, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        /** @var User $user */
        $user = $this->getUser();
        $registrationsTotal = $em->getRepository(FoRegistration::class)->findBy(['session' => $session]);
        $registrations = $em->getRepository(FoRegistration::class)->findBy(['session' => $session, 'user' => $user]);

        $mpdf = $fileCreator->initPDF("Attestations");
        $mpdf = $fileCreator->addCustomStyle($mpdf, "attestation.css");

        $i = 0;
        foreach($registrations as $registration){
            if($i != 0){
                $mpdf->AddPage();
            }
            $mpdf = $fileCreator->writePDF($mpdf, "user/pdf/attestation.html.twig", [
                'formation' => $session->getFormation(),
                'session' => $session,
                'worker' => $registration->getWorker(),
                'user' => $user,
                'totalWorkers' => count($registrationsTotal)
            ]);

            $i++;
        }

        $mpdf = $fileCreator->outputPDF($mpdf, 'attestations.pdf');

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }

    /**
     * Generate emargements
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/emargements/{slug}", name="emargements", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param FoSession $session
     * @param FileCreator $fileCreator
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     * @throws MpdfException
     * @throws Exception
     */
    public function emargements(FoSession $session, FileCreator $fileCreator, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        /** @var User $user */
        $user = $this->getUser();
        $registrations = $em->getRepository(FoRegistration::class)->findBy(['session' => $session]);

        $workers = [];
        foreach($registrations as $registration){
            $worker = $registration->getWorker();
            $identifiant = $worker->getLastname() . $worker->getId();
            $workers[$identifiant] = $worker;
        }

        ksort($workers);

        $mpdf = $fileCreator->initPDF("Emargements");
        $mpdf = $fileCreator->addCustomStyle($mpdf, "emargement.css");

        $mpdf = $fileCreator->writePDF($mpdf, "user/pdf/emargement.html.twig", [
            'formation' => $session->getFormation(),
            'session' => $session,
            'workers' => $workers,
            'user' => $user
        ]);

        $mpdf = $fileCreator->outputPDF($mpdf, 'emargements.pdf');

        return $apiResponse->apiJsonResponseSuccessful("ok");
    }
}
