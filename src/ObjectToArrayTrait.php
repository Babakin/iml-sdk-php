<?php


namespace IMLSdk;

/**
 * Trait ObjectToArrayTrait
 * @package IMLSdk
 */
trait ObjectToArrayTrait
{
    /**
     * @return array
     */
    public function toArray() :array {
        $arr = get_object_vars($this);
        foreach ($arr as $id=>$prop){
            if(is_null($prop)) unset ($arr[$id]);
        }
        return $arr;
    }
}
