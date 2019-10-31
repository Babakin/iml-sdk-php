<?php


namespace IMLSdk;

/**
 * Class Container
 * @package IMLSdk
 */


class Factory
{
    /**
     * @return Guzzle
     */
    public function getCurl() :ICurl{
        return new Guzzle();
    }

    /**
     * @return Point
     */
    public function getPoint() :Point{
        return new Point;
    }

    /**
     * @return Condition
     */
    public function getCondition() :Condition{
        return new Condition;
    }

    /**
     * @return City
     */
    public function getCity() :City{
        return new City;
    }


    /**
     * @return Location
     */
    public function getLocation() :Location{
        return new Location;
    }


    /**
     * @param string $type
     * @return Collection
     * @throws ExceptionIMLClient
     */
    public function getCollection(string $type) :Collection{
        $class = __NAMESPACE__.'\\'.$type . 'Collection';
        if (!class_exists($class)) {
            throw new ExceptionIMLClient('Неверный тип коллекции '.$class);
        }
        return new $class;
    }


    /**
     * @return Order
     */
    public function getOrder() :Order{
        return new Order;
    }


    /**
     * @return Package
     * @throws \Exception
     */
    public function getPackage() :Package{
        return new Package;
    }


    /**
     * @return Item
     */
    public function getItem() :Item{
        return new Item;
    }
}
