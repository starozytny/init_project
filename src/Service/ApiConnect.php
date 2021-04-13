<?php


namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiConnect
{
    private $maps_api; //scope client

    public function __construct(HttpClientInterface $maps_api)
    {
        $this->maps_api = $maps_api;
    }

    public function connect()
    {
        $response = $this->maps_api->request(
            'GET',
            'https://api.demodirecte.fr/api/maps/data'
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
//        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }
}