<?php

namespace App\Controller\Api\Formation;

use App\Entity\Formation\FoRegistration;
use App\Entity\Formation\FoSession;
use App\Entity\Formation\FoWorker;
use App\Entity\Paiement\PaOrder;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Data\Paiement\DataPaiement;
use App\Service\ValidatorService;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/registration", name="api_registration_")
 */
class RegistrationController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Create registration worker-session
     *
     * @Route("/{session}", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param Request $request
     * @param FoSession $session
     * @param ValidatorService $validator
     * @param ApiResponse $apiResponse
     * @param DataPaiement $dataPaiement
     * @return JsonResponse
     */
    public function create(Request $request, FoSession $session, ValidatorService $validator, ApiResponse $apiResponse,
                           DataPaiement $dataPaiement): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $workers = $em->getRepository(FoWorker::class)->findBy(['id' => $data->workersId]);

        foreach($workers as $worker){

            $obj = (new FoRegistration())
                ->setUser($user)
                ->setFormation($session->getFormation())
                ->setSession($session)
                ->setWorker($worker)
            ;

            $em->persist($obj);

            $noErrors = $validator->validate($obj);
            if ($noErrors !== true) {
                return $apiResponse->apiJsonResponseValidationFailed($noErrors);
            }
        }

        $nameOrder = $session->getFormation()->getName() . " " . $session->getFullDateHuman();
        $nameOrder = strlen($nameOrder) < 255 ? $nameOrder : $session->getFormation()->getName() . " #" . $session->getId();

        $bank = $data->bank;

        $dataOrder = [
            "price" => $session->getPriceTTC() * count($workers),
            "name" => $nameOrder,
            "titulaire" => $bank->titulaire,
            "iban" => $bank->iban,
            "bic" => $bank->bic,
            "email" => $user->getEmail(),
            "participants" => 1,
            "address" => "ADRESSE A COMPLETER",
            "zipcode" => "ADRESSE A COMPLETER",
            "city" => "ADRESSE A COMPLETER"
        ];
        $dataOrder = json_decode(json_encode($dataOrder));

        $code = uniqid();
        $order = $dataPaiement->setDataOrder(new PaOrder(), $dataOrder, $user, $user->getId().time(), $code, $request->getClientIp());

        $noErrors = $validator->validate($order);
        if ($noErrors !== true) {
            return $apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        // send mail

        $em->persist($order);
//        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Success");
    }

    /**
     * Switch attestation
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/attestation/{id}", name="switch_attestation", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param FoRegistration $obj
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function switchAttestation(FoRegistration $obj, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj->setHaveAttestation(!$obj->getHaveAttestation());
        $em->flush();

        return  $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Enable all attestation
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/attestations/{session}", name="enable_attestations", options={"expose"=true}, methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Registration")
     *
     * @param FoSession $session
     * @param ApiResponse $apiResponse
     * @return RedirectResponse
     */
    public function enableAttestations(FoSession $session, ApiResponse $apiResponse): RedirectResponse
    {
        $em = $this->doctrine->getManager();
        $registrations = $em->getRepository(FoRegistration::class)->findBy(['session' => $session]);
        foreach($registrations as $obj){
            $obj->setHaveAttestation(true);
        }

        $em->flush();

        return $this->redirectToRoute('admin_sessions_read', ['slug' => $session->getSlug()]);
    }
}
