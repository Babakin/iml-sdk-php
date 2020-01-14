<?php


namespace IMLSdk;


trait ObjectGetterMethodTrait
{
    use SplitStringCamesCase;
    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws ExceptionIMLClient
     */
    public function __call($method, $arguments){
        $prop = $this->stringSplitCamelCase($method,'get');
        if(property_exists($this,ucfirst($prop))){
            $prop = ucfirst($prop);
            return $this->$prop;
        }elseif (property_exists($this,$prop)){
            return $this->$prop;
        }
        return;
    }

}