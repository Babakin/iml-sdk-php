<?php


namespace IMLSdk;

/**
 * IoC для курловой библиотеки
 * Interface ICurl
 * @package IMLSdk
 */
interface ICurl
{
    /**
     * @param string $url
     * @param string $method
     * @param string $login
     * @param string $password
     * @param array $data
     * @return IMLResponse
     */
    public function sendRequest(string $url, string $method, $login, $password ,array $data=[]) :IMLResponse;


    /**
     * @return void
     */
    public function debugMode() :void;
    
    
/**
     * @param string $url
     * @param string $method
     * @param array $data
     * @return IMLResponse
     */
    public function sendNonAuthRequest(string $url, string $method, array $data=[]) :IMLResponse;    
    
    
}
