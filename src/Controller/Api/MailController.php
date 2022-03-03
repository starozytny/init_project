<?php

namespace App\Controller\Api;

use App\Entity\Mail;
use App\Repository\UserRepository;
use App\Service\ApiResponse;
use App\Service\Data\DataService;
use App\Service\FileUploader;
use App\Service\MailerService;
use App\Service\SettingsService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/mails", name="api_mails_")
 */
class MailController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Admin - Preview mail
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/preview", name="preview", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns array of contacts",
     * )
     * @OA\Tag(name="Mails")
     *
     * @param Request $request
     * @param SettingsService $settingsService
     * @param ApiResponse $apiResponse
     * @return Response
     */
    public function preview(Request $request, SettingsService $settingsService, ApiResponse $apiResponse): Response
    {
        $data = json_decode($request->get('data'));

        if($data == null){
            return $apiResponse->apiJsonResponseBadRequest("Les données sont vides.");
        }

        $html = $this->renderView('app/email/template/random.html.twig', [
            'subject' => $data->subject,
            'title' => $data->title,
            'message' => $data->message->html,
            'settings' => $settingsService->getSettings()
        ]);

        return new Response($html);
    }

    /**
     * Create a message contact
     *
     * @Route("/create", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Mails")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @param MailerService $mailerService
     * @param SettingsService $settingsService
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function create(Request $request, ApiResponse $apiResponse, MailerService $mailerService,
                                   SettingsService $settingsService, FileUploader $fileUploader): JsonResponse
    {
        $data = json_decode($request->get('data'));

        if ($data == null) {
            return $apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $files = [];
        for($i = 0; $i <= 5 ; $i++){
            $file = $request->files->get('file' . $i);
            if($file){
                $f = $fileUploader->upload($file, "emails", false);
                $files[] = $fileUploader->getPrivateDirectory() . "emails/" .$f;
            }
        }

        $from = $data->from ?? null;
        $to = $this->getEmails($data->to);
        $cc = $this->getEmails($data->cc);
        $bcc = $this->getEmails($data->bcc);

        $html = $data->theme == 0 ? "random_classique" : "random";
        $params = $data->theme == 1 ? ['title' => trim($data->title)] : [];

        if($mailerService->sendMailAdvanced(
                $to, $cc, $bcc,
                "[" . $settingsService->getWebsiteName() ."] " . trim($data->subject),
                trim($data->subject),
                'app/email/template/' . $html . '.html.twig',
                array_merge($params,  [
                    'subject' => trim($data->subject),
                    'message' => trim($data->message->html),
                    'settings' => $settingsService->getSettings()
                ]),
                $files,
                $from
            ) != true)
        {
            return $apiResponse->apiJsonResponseValidationFailed([[
                'name' => 'message',
                'message' => "Le message n\'a pas pu être délivré. Veuillez contacter le support."
            ]]);
        }

        foreach($files as $file){
            if(file_exists($file)){
                unlink($file);
            }
        }

        return $apiResponse->apiJsonResponseSuccessful("Message envoyé. La page va se rafraichir automatiquement dans 3 secondes.");
    }

    private function getEmails($data): array
    {
        $emails = [];
        foreach($data as $email){
            $emails[] = $email->value;
        }

        return $emails;
    }

    /**
     * @Route("/trash/{id}", name="trash", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Mails")
     *
     * @param Mail $obj
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function trash(Mail $obj, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj->setStatus(Mail::STATUS_TRASH);
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, Mail::MAIL_READ);
    }

    /**
     * @Route("/restore/{id}", name="restore", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Mails")
     *
     * @param Mail $obj
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function restore(Mail $obj, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $obj->setStatus($obj->getStatusOrigin());
        $em->flush();

        return $apiResponse->apiJsonResponse($obj, Mail::MAIL_READ);
    }

    /**
     * @Route("/delete/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Mails")
     *
     * @param Mail $obj
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function delete(Mail $obj, DataService $dataService): JsonResponse
    {
        return $dataService->delete($obj);
    }

    /**
     * @Route("/trash-group", name="trash_group", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Mails")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function trashGroup(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        $objs = $em->getRepository(Mail::class)->findBy(['id' => $data]);
        foreach($objs as $obj){
            $obj->setStatus(Mail::STATUS_TRASH);
        }
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Ok");
    }

    /**
     * @Route("/restore-group", name="restore_group", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Mails")
     *
     * @param Request $request
     * @param ApiResponse $apiResponse
     * @return JsonResponse
     */
    public function restoreGroup(Request $request, ApiResponse $apiResponse): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $data = json_decode($request->getContent());

        $objs = $em->getRepository(Mail::class)->findBy(['id' => $data]);
        foreach($objs as $obj){
            $obj->setStatus($obj->getStatusOrigin());
        }
        $em->flush();

        return $apiResponse->apiJsonResponseSuccessful("Ok");
    }

    /**
     * @Route("/delete-group", name="delete_group", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a message",
     * )
     *
     * @OA\Tag(name="Mails")
     *
     * @param Request $request
     * @param DataService $dataService
     * @return JsonResponse
     */
    public function deleteGroup(Request $request, DataService $dataService): JsonResponse
    {
        return $dataService->deleteSelected(Mail::class, json_decode($request->getContent()));
    }
}
