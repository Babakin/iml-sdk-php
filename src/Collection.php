<?php


namespace IMLSdk;


use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;
use Countable;

abstract class Collection implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * Тип элементов, хранящихся в данной коллекции.
     * @var string
     */
    protected $type;


    /**
     * Хранилище объектов
     * @var array
     */
    protected $collection = [];


    /**
     * Проверяет тип объекта.
     * Препятствует добавлению в коллекцию объектов `чужого` типа.
     *
     * @param object $object Объект для проверки
     * @throws ExceptionIMLClient
     */
    protected function checkType(&$object) {
        try{
            if ((new \ReflectionClass($object))->getShortName() != $this->type) {
                throw new ExceptionIMLClient('Объект типа `' . get_class($object)
                    . '` не может быть добавлен в коллекцию объектов типа `' . $this->type . '`');
            }
        }catch (\Exception $e){
            throw new ExceptionIMLClient('Ошибка данных коллекции ');
        }

    }


    /**
     * Добавляет в коллекцию объекты, переданные в аргументах.
     *
     * @param object(s)  Объекты
     * @return mixed Collection
     * @throws ExceptionIMLClient
     */
    public function add() :Collection{
        $args = func_get_args();
        foreach ($args as $arg) {
            if(is_array($arg)){
                foreach ($arg as $obj){
                    $this->add($obj);
                }
            }
            $this->checkType($arg);
            $this->collection[] = $arg;
        }
        return $this;
    }


    /**
     * Удаляет из коллекции объекты, переданные в аргументах.
     *
     * @param object(s)  Объекты
     * @return mixed Collection
     */
    public function remove() :Collection
    {
        $args = func_get_args();
        foreach ($args as $object) {
            unset($this->collection[array_search($object, $this->collection)]);
        }
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function first(){
        if(isset ($this->collection[0])) return $this->collection[0];
        return null;
    }


    /**
     * Очищает коллекцию.
     *
     * @return mixed Collection
     */
    public function clear() :Collection {
        $this->collection = [];
        return $this;
    }


    /**
     * @return bool
     */
    public function isEmpty() :bool {
        return empty($this->collection);
    }



    /**
     * Возвращает объект итератора.
     * Реализация IteratorAggregate
     * @return ArrayIterator
     */
    public function getIterator() :ArrayIterator {
        return new ArrayIterator($this->collection);
    }


    /**
     * Sets an element of collection at the offset
     * Реализация ArrayAccess.
     * @param integer $offset Offset
     * @param $object
     * @return void
     * @throws ExceptionIMLClient
     */
    public function offsetSet($offset, $object) :void {
        $this->checkType($object);
        if ($offset === NULL) {
            $offset = max(array_keys($this->collection)) + 1;
        }
        $this->collection[$offset] = $object;
    }

    /**
     * Выясняет существует ли элемент с данным ключом.
     * Реализация ArrayAccess
     * @param integer $key  Ключ
     * @return bool
     */
    public function offsetExists($key) :bool {
        return isset($this->collection[$key]);
    }

    /**
     * Удаляет элемент, на который ссылается ключ $offset.
     * Реализация ArrayAccess
     * @param integer $key
     * @return void
     */
    public function offsetUnset($key) :void {
        unset($this->collection[$key]);
    }

    /**
     * Возвращает элемент по ключу.
     * Реализация ArrayAccess
     * @param integer $key  Ключ
     * @return mixed
     */
    public function offsetGet($key) {
        if(isset($this->collection[$key]) === false){
            return NULL;
        }
        return $this->collection[$key];
    }



    /**
     * Возвращает кол-во элементов в коллекции.
     * Реализация Countable
     *
     * @return integer
     */
    public function count() :int {
        return count($this->collection);
    }

}