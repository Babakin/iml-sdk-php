<?php


namespace IMLSdk;

/**
 * DI
 * Interface ICurlInject
 * @package IMLSdk
 */
interface ICurlInject
{
    /**
     * @param ICurl $curl
     * @return void
     */
    public function injectCurl(ICurl $curl) :void;

    /**
     * @param bool $debug
     * @return void
     */
    public function curlDebugMode(bool $debug) :void;
}
