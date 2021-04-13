<?php


namespace App\Service;


use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiConnect
{
    private $api_immo; //scope client
    private $urlImmo;

    public function __construct(HttpClientInterface $api_immo, $urlImmo)
    {
        $this->api_immo = $api_immo;
        $this->urlImmo = $urlImmo;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function connect($method, $url): string
    {
        $response = $this->api_immo->request($method, $url);

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'

        // $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        //  $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $response->getContent();
    }

    public function getUrlApiImmo()
    {
        return $this->urlImmo;
    }
}