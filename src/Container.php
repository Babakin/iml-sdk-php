<?php


namespace IMLSdk;

/**
 * Class Container
 * @package IMLSdk
 */
class Container
{
    /**
     * @return Guzzle
     */
    public function getCurl(){
        return new Guzzle();
    }

    /**
     * Формирует объект Point из массива значений свойств этого объекта.
     * Массив значений возвращает IML
     * @param array $data
     * @return Point
     */
    public function getPoint(array $data){
        return Point::buildPoint($data);
    }

    /**
     * @param object $data
     * @return Condition
     */
    public function getCondition(object $data){
        return Condition::buildCondition($data);
    }

}
