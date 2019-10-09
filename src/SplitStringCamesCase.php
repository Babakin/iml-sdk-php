<?php


namespace IMLSdk;


trait SplitStringCamesCase
{
    /**
     * @param string $string
     * @param string $prefix
     * @return string
     */
    public function stringSplitCamelCase(string $string,string $prefix):string {
        $array = preg_split('/(?=[A-Z])/',$string);
        if($array[0] == $prefix){
            unset($array[0]);
        }
        return lcfirst(implode($array));
    }
}