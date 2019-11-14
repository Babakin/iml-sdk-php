<?php


namespace IMLSdk;


abstract class BaseObject
{
    /**
     * @param array $data
     * @return mixed
     */
    public function init(array $data) :BaseObject{
        $object = new static();
        $data = (array)$data;
        $keys = array_keys($data);
        foreach (get_object_vars($object) as $prop=>$val){
            if(in_array($prop,$keys)){
                $object->$prop = $data[$prop];
            }
        }
        return $object;
    }

}