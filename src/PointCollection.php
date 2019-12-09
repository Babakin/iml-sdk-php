<?php


namespace IMLSdk;


class PointCollection extends Collection
{
	protected $type = 'Point';
	// коэффициент совпадения при поиске по городу или региону
	const SIM_MIN_COEF = 90;  
	
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
	
	
	private function hasCashService($item)
	{
		return $item['PaymentPossible'] == 1;
	}
	

    private function cmpByAddr($a, $b)
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



	public function sortByAddr()
	{
		uasort($this->collection, array($this, 'cmpByAddr'));
		return $this;
	}
	
	private function clearPlaceName($placeName)
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
	
	
	public function findByPlace($city, $region = null, $job = null)
	{
		// $city = trim(mb_strtoupper(str_ireplace('ё', 'е', $city)));
		// $city = str_ireplace(['Г.', 'город'], '', $city);
		// if($region)
		// {
		// 	$region = trim(mb_strtoupper(str_ireplace('ё', 'е', $region)));
		// 	$region = str_ireplace ( ['РЕСП.', 'КРАЙ.', 'ОБЛ.'], ['РЕСПУБЛИКА', 'КРАЙ', 'ОБЛАСТЬ'], $region);
		// 	$region = str_ireplace(['Г.', 'город'], '', $region);
		// }
		
		
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
			
			$upperFormCity = $this->clearPlaceName($item->getFormCity());
			$upperFormRegion = $this->clearPlaceName($item->getFormRegion());			
			
			if(empty($upperFormCity) && !empty($upperFormRegion))
			{
				$upperFormCity = $upperFormRegion;
			}

			if(!empty($upperFormCity) && empty($upperFormRegion))
			{
				$upperFormRegion = $upperFormCity;
			}

			
			if($region)
			{
				similar_text($city, $upperFormCity, $percCity);
				similar_text($region, $upperFormRegion, $percRegion);
				if($percCity > self::SIM_MIN_COEF &&
					$percRegion > self::SIM_MIN_COEF)
				{
					
					$foundedCollection[] = $item;
				}
			}else {
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