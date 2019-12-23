<?php
namespace IMLSdk;

class PointCollection extends Collection
{
	protected $type = 'Point';

	// коэффициент совпадения при поиске по городу или региону
	const SIM_MIN_COEF = 90;  
	
	/**
	 * Поиск ПВЗ по полю ID
	 *
	 * @param [int] $ID
	 * @return boolean|Point
	 */
	public function findByID($ID)
	{
		foreach ($this as $key => $item)
		{
			if($item->getID() == $ID)
			{
				return $item;
			}
		}
		return false;
	}


	/**
	 * получить  первый ПВЗ, у которого возможно кассовое обслуживание
	 *
	 * @return boolean|Point 
	 */
	public function firstC24KO()
	{
		foreach ($this as $key => $item)
		{
			if(!empty($item->getPaymentType()))
			{
				return $item;
			}
		}
		return false;
	}
	
	/**
	 * проверка того, что ПВЗ имеет кассовое обслуживание 
	 *
	 * @param [array] $item
	 * @return boolean
	 */
	private function hasCashService(array $item):boolean
	{
		return $item['PaymentPossible'] == 1;
	}
	
	/**
	 * Функция для сверки позиций двух ПВЗ при сортировке в зависимости от полей адреса
	 *
	 * @param [Point] $a
	 * @param [Point] $b
	 * @return int
	 */
    private function cmpByAddr(Point $a, Point $b):int
    {

		$addrA = $a->getFormAddress();
		$addrB = $b->getFormAddress();
		$addrA = mb_strtoupper($addrA, 'UTF-8');
		$addrB = mb_strtoupper($addrB, 'UTF-8');
		


        $isMoscowA = mb_stripos($addrA, 'МОСКВА Г.', 0,  'UTF-8') !== false;
        $isSpbA = mb_stripos($addrA, 'САНКТ-ПЕТЕРБУРГ Г.',0, 'UTF-8') !== false;


        $isMoscowB = mb_stripos($addrB, 'МОСКВА Г.',0, 'UTF-8') !== false;
        $isSpbB = mb_stripos($addrB, 'САНКТ-ПЕТЕРБУРГ Г.',0, 'UTF-8') !== false;


        if ($isMoscowA && $isSpbB) {
            return -1;
        } else if ($isMoscowB && $isSpbA) {
            return 1;
		} 
		else if (($isMoscowA || $isSpbA) && !($isMoscowB || $isSpbB))
		{
            return -1;
        } else if (($isMoscowB || $isSpbB) && !($isMoscowA || $isSpbA)) {
            return 1;
		} 
		else {
            return strcmp($addrA, $addrB);
        }

    }


	/**
	 * Функция для сортировки текущего списка ПВЗ 
	 * сначала Москва и Санкт-Петербург  - затем в алфавитном порядке - остальные 
	 *
	 * @return void
	 */
	public function sortByAddr():PointCollection
	{
		uasort($this->collection, array($this, 'cmpByAddr'));
		return $this;
	}
	
	/**
	 * Очистка названий города или региона от ненужных для поиска фрагментов и перевод в верхний регистр
	 *
	 * @param [string] $placeName
	 * @return string
	 */
	private function clearPlaceName(string $placeName):string
    {
        // ___p($placeName);
        $clearAr = [' ГОРОД', 'АО - ЮГРА', 'РЕСП.', ' КРАЙ.', ' ОБЛ.', 'РЕСПУБЛИКА', ' КРАЙ', ' ОБЛАСТЬ', 
        'АВТОНОМНЫЙ ОКРУГ', ' АО.', ' Г.'];
        $placeName = trim(mb_strtoupper(str_ireplace('ё', 'е', $placeName)));
        $placeName = str_ireplace($clearAr, '', $placeName);   
        // $placeName = str_ireplace ([' РЕСП.', ' КРАЙ.', ' ОБЛ.'], [' РЕСПУБЛИКА', ' КРАЙ', ' ОБЛАСТЬ'], $placeName);

        // ___p($placeName);
         return $placeName;
    }	
	
	

	/**
	 * Поиск в списке ПВЗ определенных ПВЗ, относящихся к данному городу и региону  и услуге (job)
	 *
	 * @param [string] $city
	 * @param [string] $region
	 * @param [string] $job
	 * @return PointCollection
	 */
	public function findByPlace(string $city, string $region = null, string $job = null):PointCollection
	{
		
		// преобразование поисковых города и региона
		$city = $this->clearPlaceName($city);
		if($region)
		{
			$region = $this->clearPlaceName($region);
		}
		

		$foundedCollection  = [];
		
		foreach ($this as $key => $item)
		{


			if($job && $job == 'С24КО' && !$this->hasCashService($item))
			{
				continue;
			}
			
			// преобразование города и региона из цикла
			$upperFormCity = $this->clearPlaceName($item->getFormCity());
			$upperFormRegion = $this->clearPlaceName($item->getFormRegion());			
			

			// если что-то из них не указано - присвоить другому (правило для столиц)
			if(empty($upperFormCity) && !empty($upperFormRegion))
			{
				$upperFormCity = $upperFormRegion;
			}

			if(!empty($upperFormCity) && empty($upperFormRegion))
			{
				$upperFormRegion = $upperFormCity;
			}

			// получение коэффициентов подобия и сравнение с коэффициентом совпадения 
			if($region)
			{
				similar_text($city, $upperFormCity, $percCity);
				similar_text($region, $upperFormRegion, $percRegion);
				if($percCity > self::SIM_MIN_COEF &&
					$percRegion > self::SIM_MIN_COEF)
				{
					
					$foundedCollection[] = $item;
				}
			}else 
			{
				similar_text($city, $upperFormCity, $percCity);
				if($percCity > self::SIM_MIN_COEF)
				{
					$foundedCollection[] = $item;
				}
			}

		}

		$this->collection = $foundedCollection;
		return $this;    	
	}

}