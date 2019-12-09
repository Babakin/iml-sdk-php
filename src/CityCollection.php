<?php


namespace IMLSdk;


class CityCollection extends Collection
{
    protected $type = 'City';
    // при сортировке по городу - столицы  в начале списка
    private $setCapitalsAtBeginning = true;


    private function cmpByCity($a, $b)
    {

		$cityA = $a->City;
		$cityB = $b->City;

        $isMoscowA = mb_stripos($cityA, 'МОСКВА', 0,  'UTF-8') !== false;
        $isSpbA = mb_stripos($cityA, 'САНКТ-ПЕТЕРБУРГ',0, 'UTF-8') !== false;


        $isMoscowB = mb_stripos($cityB, 'МОСКВА',0, 'UTF-8') !== false;
        $isSpbB = mb_stripos($cityB, 'САНКТ-ПЕТЕРБУРГ',0, 'UTF-8') !== false;

        if ($isMoscowA && $isSpbB) {
            return -1;
        } else if ($isMoscowB && $isSpbA) {
            return 1;
        } else if ($isMoscowA || $isSpbA) {
            return -1;
        } else if ($isMoscowB || $isSpbB) {
            return 1;
        } else {
            return strcmp($cityA, $cityB);
        }

    }




    public function sortByCity($setCapitalsAtBeginning)
    {
        $this->setCapitalsAtBeginning = $setCapitalsAtBeginning;
		uasort($this->collection, array($this, 'cmpByCity'));
		return $this;
    }

}