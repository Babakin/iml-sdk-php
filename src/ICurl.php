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
    public function sendRequest(string $url, string $method, string $login, string $password ,array $data=[]) :IMLResponse;


    /**
     * @return void
     */
    public function debugMode() :void;
}
