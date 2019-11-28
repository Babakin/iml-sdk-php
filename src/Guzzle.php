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
     * @throws ExceptionIMLClient
     */
    public function sendRequest(string $url, string $method = 'GET',  $login,  $password, array $data=[], $convertResultFromJson = true, float $connect_timeout = 0) :IMLResponse{
        try{
            $client = new Client(['base_uri' => $url,'exceptions' => false,'debug' => $this->debug,
                'headers' => [ 'Content-Type' => 'application/json' ]
            ]);

            $responseGuzzle = $client->request($method, '', ['auth' => [$login, $password], //'json'=>$data,
                                               'connect_timeout' => $connect_timeout,
                                                'body' => json_encode($data, JSON_UNESCAPED_UNICODE)
                                           ]);
            
            if($convertResultFromJson)
            {
                return $this->convert($responseGuzzle);
            }else
            {
                return new IMLResponse($responseGuzzle->getReasonPhrase(),$responseGuzzle->getStatusCode(),$responseGuzzle->getBody()->getContents());
            }
            
        }catch (\Exception $e){
            throw new ExceptionIMLClient('Ошибка отправки запроса Curl '.$e->getMessage());
        }
    }

    /**
     * Активация debug режима
     * @return void
     */
    public function debugMode(): void{
        $this->debug = true;
    }

    /**
     * @param Response $response
     * @return IMLResponse
     */
    protected function convert(Response $response) :IMLResponse{
        $content = json_decode($response->getBody()->getContents(),true);
        return new IMLResponse($response->getReasonPhrase(),$response->getStatusCode(),$content);
    }
    
    
        /**
     * @param string $url
     * @param string $method
     * @param array $data
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function sendNonAuthRequest(string $url, string $method = 'GET', array $data=[]) :IMLResponse{
        try{
            $client = new Client(['base_uri' => $url,'exceptions' => false,'debug' => $this->debug]);
            $responseGuzzle = $client->request($method, '', ['json'=>$data]);
            return $this->convert($responseGuzzle);
        }catch (\Exception $e){
            throw new ExceptionIMLClient('Ошибка отправки запроса Curl '.$e->getMessage());
        }
    }

    
}
