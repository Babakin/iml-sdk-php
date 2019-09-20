<?php


namespace IMLSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Class Guzzle
 * @package IMLSdk
 */
class Guzzle implements ICurl
{

    /**
     * Включает debug режим curl библиотеки.
     * При значении true выводит все значения запроса на экран.
     * @var bool
     */
    private $debug = false;

    /**
     * @param string $url
     * @param string $method
     * @param string $login
     * @param string $password
     * @param array $data
     * @return IMLResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest(string $url, string $method = 'GET', string $login, string $password, array $data=[]) :IMLResponse{
        $client = new Client(['base_uri' => $url,'exceptions' => false,'debug' => $this->debug]);
        $responseGuzzle = $client->request($method, '', ['auth' => [$login, $password],'json'=>$data]);
        return $this->convert($responseGuzzle);
    }

    /**
     * Активация debug режима
     * @return void
     */
    public function debug(): void{
        $this->debug = true;
    }

    /**
     * @param Response $response
     * @return IMLResponse
     */
    protected function convert(Response $response) :IMLResponse{
        $content = (array)json_decode($response->getBody()->getContents());
        return new IMLResponse($response->getReasonPhrase(),$response->getStatusCode(),$content);
    }
}
