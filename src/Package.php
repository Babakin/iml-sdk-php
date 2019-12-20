<?php


namespace IMLSdk;


/**
 * Грузовое место
 * Class Package
 * @package IMLSdk
 */
class Package
{
    use ObjectToArrayTrait;

    /**
     * Заполняется для внутренней идентификации записи
     * @var string
     */
    private $productNo;

    /**
     * Служебное поле
     * @var int
     */
    private $itemType = 7;

    /**
     * Сторона грузового места в см
     * @var float
     */
    private $Side1;

    /**
     * Сторона грузового места в см
     * @var float
     */
    private $Side2;

    /**
     * Сторона грузового места в см
     * @var float
     */
    private $Side3;

    /**
     * Вес передаваемого грузового места
     * @var float
     */
    private $weightLine;

    /**
     * @var array Item[]
     */
    private $items = [];


    /**
     * Package constructor.
     * @throws \Exception
     */
    public function __construct(){
        $this->productNo = random_int(1111,9999);
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function setItem(Item $item){
        for ($i = 1; $i <= $item->getQuantity(); $i++) {
            $this->weightLine += $item->getWeightLine();
        }
        $this->items = array_merge($this->items,[$item]);
        return $this;
    }

    /**
     * Стороны грузового места
     * @param float $length
     * @param float $width
     * @param float $height
     * @return $this
     */
    public function setPackageDimensions(float $length, float $width, float $height){
        $this->setSide1($length);
        $this->setSide2($width);
        $this->setSide3($height);
        return $this;
    }

    /**
     * @param float $side1
     */
    private function setSide1(float $side1){
        $this->Side1 = $side1;
    }

    /**
     * @param float $side2
     */
    private function setSide2(float $side2){
        $this->Side2 = $side2;
    }

    /**
     * @param float $side3
     */
    private function setSide3(float $side3){
        $this->Side3 = $side3;
    }

    /**
     * @return array
     */
    public function getPackage(){
        $arr = $this->toArray();
        unset($arr['items']);
        return $arr;
    }

    /**
     * @return array
     */
    public function getItems(){
        return $this->items;
    }

    /**
     * @return float
     */
    public function getWeightLine(){
        return $this->weightLine;
    }
    
    /**
     * Указание веса грузового места
     *
     * @param float $weightLine
     * @return void
     */
    public function setWeightLine(float $weightLine){
        $this->weightLine = $weightLine;
    }    
    
}
