<?php 
namespace IMLSdk\Filters;

class PickPointsFilter extends Filter
{
	
	public function filterCollection():array
	{
	  $newData = [];
	  $federalCities = ['МОСКВА Г.', 'САНКТ-ПЕТЕРБУРГ Г.', 'МОСКВА', 'САНКТ-ПЕТЕРБУРГ', 'СЕВАСТОПОЛЬ'];
	  $pickPointChecker = new PickPointChecker($federalCities);
      foreach ($this->collection as $pickpoint) 
      {
		if($pickPointChecker->isCorrectPickpoint($pickpoint))
		{
			$newData[] = $pickpoint;	
		}
      }	
      return $newData;
		
	}
	
	
}