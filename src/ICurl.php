<?php


namespace IMLSdk;

/**
 * IoC для курловой библиотеки
 * Interface ICurl
 * @package IMLSdk
 */
interface ICurl
{
    public function sendRequest(string $url, string $method, string $login, string $password ,array $data=[]) :IMLResponse;

    public function debug() :void;
}
