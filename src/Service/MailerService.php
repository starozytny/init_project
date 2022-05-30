<?php


namespace App\Service;


use App\Entity\Bill\BiAvoir;
use App\Entity\Bill\BiInvoice;
use App\Entity\Bill\BiQuotation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class MailerService
{
    private $mailer;
    private $settingsService;
    private $router;

    public function __construct(MailerInterface $mailer, SettingsService $settingsService, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->settingsService = $settingsService;
        $this->router = $router;
    }

    public function sendMail($to, $subject, $text, $html, $params, $from=null)
    {
        $from = ($from == null) ? $this->settingsService->getEmailExpediteurGlobal() : $from;

        $email = (new TemplatedEmail())
            ->from($from)
            ->to(new Address($to))
            ->subject($subject)
            ->text($text)
            ->htmlTemplate($html)
            ->context($params)
        ;

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            return 'Le message n\'a pas pu être délivré. Veuillez contacter le support.';
        }
    }


    public function sendInvoice(BiInvoice $obj): bool
    {
        if($obj->getToEmail() ||$obj->getSiEmail()){
            if(!$this->sendMail(
                $obj->getSiEmail() ?: $obj->getToEmail(),
                "[" . $this->settingsService->getWebsiteName() . "] Facture",
                "Facture venant de " . $this->settingsService->getWebsiteName(),
                'app/email/bill/invoice.html.twig',
                [
                    'elem' => $obj,
                    'settings' => $this->settingsService->getSettings(),
                    'url' => $this->router->generate('app_bill_invoice', ['id' => $obj->getId(), 'uid' => $obj->getUid()], UrlGeneratorInterface::ABSOLUTE_URL),
                ]
            )) {
                return false;
            }
        }

        return true;
    }

    public function sendQuotation(BiQuotation $obj): bool
    {
        if($obj->getToEmail() ||$obj->getSiEmail()){
            if(!$this->sendMail(
                $obj->getSiEmail() ?: $obj->getToEmail(),
                "[" . $this->settingsService->getWebsiteName() . "] Devis",
                "Devis venant de " . $this->settingsService->getWebsiteName(),
                'app/email/bill/quotation.html.twig',
                [
                    'elem' => $obj,
                    'settings' => $this->settingsService->getSettings(),
                    'url' =>$this->router->generate('app_bill_quotation', ['id' => $obj->getId(), 'uid' => $obj->getUid()], UrlGeneratorInterface::ABSOLUTE_URL),
                ]
            )) {
                return false;
            }
        }

        return true;
    }

    public function sendAvoir(BiAvoir $obj): bool
    {
        if($obj->getToEmail() ||$obj->getSiEmail()){
            if(!$this->sendMail(
                $obj->getSiEmail() ?: $obj->getToEmail(),
                "[" . $this->settingsService->getWebsiteName() . "] Avoir",
                "Avoir venant de " . $this->settingsService->getWebsiteName(),
                'app/email/bill/avoir.html.twig',
                [
                    'elem' => $obj,
                    'settings' => $this->settingsService->getSettings(),
                    'url' =>  $this->router->generate('app_bill_avoir', ['id' => $obj->getId(), 'uid' => $obj->getUid()], UrlGeneratorInterface::ABSOLUTE_URL),
                ]
            )) {
                return false;
            }
        }

        return true;
    }
}