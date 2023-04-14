<?php


namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Session;

class ExternalApi
{
    protected $url;
    protected $data;
    protected $headers=[];
    protected $method="get";
    protected $body;
    protected $authorization;
    protected $mode;
    protected $client;
    protected $extra_headers=[];

    public function construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    public function url(string $url)
    {
        $this->url = $url;

        return $this;
    }

    public function body(array $data)
    {
        $this->body = $data;
        return $this;
    }

    public function method(string $method='get')
    {
        $this->method = strtolower($method);

        return $this;
    }

    public function authorization($authorization='')
    {
        $this->authorization = $authorization;
        return $this;
    }

    public function headers(array $extra_headers=[])
    {
        $this->headers = $extra_headers;
        return $this;
    }

    public function extraHeaders(array $extra_headers)
    {
        $this->extra_headers = $extra_headers;
        return $this;
    }

    public function totalHeaders()
    {
        $main_headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->authorization
        ];

        return $main_headers + $this->headers + $this->extra_headers;
    }

    public function process()
    {
        Log::info('Headers');
        Log::info(json_encode($this->totalHeaders()));
        Log::info('Body');
        Log::info(json_encode($this->body));
        
        $client = new Client([
            'base_uri' => $this->url,
            'headers' => $this->totalHeaders()
        ]);

        $requestAPI = $client->{strtolower($this->method)}($this->url,
            ['body' => json_encode($this->body)]
        );
        
        $response = json_decode($requestAPI->getBody(), true);
        return $response;
    }

}