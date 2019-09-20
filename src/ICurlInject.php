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
     * @param bool $debug
     * @return mixed
     */
    public function setCurl(ICurl $curl,bool $debug);
}
