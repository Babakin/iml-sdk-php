<?php 
namespace IMLSdk\Filters;

class PickPointsFilter extends Filter
{
	/**
	 * Фильтруем список ПВЗ из list.iml.ru/sd путем проверки с помощью PickPointChecker
     * @param array $collection фильтруемая коллекция
	 * @return array
	 */
	public function filterCollection(array $collection):array
	{
	  $newData = [];
		// города федерального назначения, которых может быть не заполнено FormCity 
	  $federalCities = ['МОСКВА Г.', 'САНКТ-ПЕТЕРБУРГ Г.', 'МОСКВА', 'САНКТ-ПЕТЕРБУРГ', 'СЕВАСТОПОЛЬ'];
	  $pickPointChecker = new PickPointChecker($federalCities);
      foreach ($collection as $pickpoint) 
      {
		if($pickPointChecker->isCorrectPickpoint($pickpoint))
		{
			$newData[] = $pickpoint;	
		}
      }	
      return $newData;		
	}
	
	
}