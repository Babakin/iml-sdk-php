<?php


namespace IMLSdk;


class City extends BaseObject
{
    /**
     * @var string
     */
    protected $City;

    /**
     * @var string
     */
    protected $Region;

    /**
     * @var string
     */
    protected $Area;

    /**
     * @var string
     */
    protected $RegionIML;

    /**
     * @var string
     */
    protected $RateZoneMoscow;

    /**
     * @var string
     */
    protected $RateZoneSpb;

    /**
     * @return string
     */
    public function getRegion(){
        return $this->RegionIML;
    }

    /**
     * @return string
     */
    public function getCityName(){
        return $this->City;
    }

}