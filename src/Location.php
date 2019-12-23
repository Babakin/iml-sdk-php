<?php

namespace IMLSdk;
/**
 * Склад IML
 */
class Location extends BaseObject
{
	
	/**
     * @var string
     */
    protected $RegionCode;	
    
    
    public function __get($property)
    {
        
        if (property_exists($this, $property)) 
        {
            return $this->$property;
        }
        
    }    
}