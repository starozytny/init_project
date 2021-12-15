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
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
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
     * Developer - Delete an order
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

    /**
     * Cancel an order
     *
     * @Route("/cancel/{id}", name="cancel", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Orders")
     *
     * @param PaOrder $obj
     * @param ApiResponse $apiResponse
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function cancel(PaOrder $obj, ApiResponse $apiResponse, DataService $dataService): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj->setStatus(PaOrder::STATUS_ANNULER);
        $obj->setUpdatedAt($dataService->createDate());
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Refresh codeAt of an order for validation
     *
     * @Route("/refresh/{id}", name="refresh", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Orders")
     *
     * @param PaOrder $obj
     * @param ApiResponse $apiResponse
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function refresh(PaOrder $obj, ApiResponse $apiResponse, DataService $dataService): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj->setCodeAt($dataService->createDate());
        $obj->setUpdatedAt($dataService->createDate());
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    /**
     * Process an order
     *
     * @Route("/process", name="process", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Orders")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function process(Request $request, ApiResponse $apiResponse, DataService $dataService): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());
        $id = $data->id;

        if($id == "all"){
            $objs = $em->getRepository(PaOrder::class)->findBy(['status' => PaOrder::STATUS_VALIDER]);
        }else{
            $objs = $em->getRepository(PaOrder::class)->findBy(['id' => $data->id, 'status' => PaOrder::STATUS_VALIDER]);
        }

        $orders = []; $total = 0; $nbOrders = 0;
        foreach($objs as $obj){
            if($obj->getPrice() > 0){
                $orders[] = $obj;
                $total += $obj->getPrice();
                $nbOrders++;
            }
        }

        $code = time();

        $fsObject = new Filesystem();
        $filename = "paiement-" . $code .".xml";

        $path = $this->getParameter('private_directory') . "/paiements";

        try {
            if (!$fsObject->exists($path)){
                $old = umask(0);
                $fsObject->mkdir($path, 0775);
                umask($old);
            }
        } catch (IOExceptionInterface $exception) {
            echo "Error creating directory at ". $exception->getPath();
        }

        try {
            $new_file_path = $path . "/" . $filename;

            if(file_exists($new_file_path)){
                unlink($new_file_path);
            }

            if (!$fsObject->exists($new_file_path)){
                $fsObject->touch($new_file_path);
                $fsObject->chmod($new_file_path, 0777);

                $fsObject->dumpFile($new_file_path, $this->renderView('admin/xml/paiements.xml.twig', array(
                    'orders' => $orders,
                    'msgId' => time() . '000' . mt_rand(10000, 99999),
                    'NbOfTxs' => $nbOrders,
                    'total' => $total,
                    'createdAt' => $dataService->createDate(),
                    'dateEcheance' => $dataService->createDate(),
                    'creancier_titulaire' => $this->getParameter('creancier.titulaire'),
                    'creancier_iban' => $this->getParameter('creancier.iban'),
                    'creancier_bic' => $this->getParameter('creancier.bic'),
                    'creancier_schmeId' => $this->getParameter('creancier.schmedid')
                )));
            }

            $em->flush();
        } catch (IOExceptionInterface $exception) {
            echo "Error creating file at ". $exception->getPath();
        }

        return $apiResponse->apiJsonResponseSuccessful("success");
    }
}
