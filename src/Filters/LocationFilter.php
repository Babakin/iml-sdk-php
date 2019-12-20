<?php 
namespace IMLSdk\Filters;

class LocationFilter extends Filter
{

    /**
     * Фильтрует склады из list.iml.ru/Location, поскольку некоторые из них не подходят для создания заказов
     * @param array $collection фильтруемая коллекция
     * @return array
     */
	public function filterCollection(array $collection):array
	{
		$newData = [];
      	foreach ($collection as $location) {

          $upperRegionCode = mb_strtoupper($location['RegionCode'], 'UTF-8');

          if (!empty($location['OpeningDate']) && strtotime($location['OpeningDate']) >= time()) {
              continue;
          }

          if ($location['RegionCode'] == 'ПОЧТА') {
              continue;
          }


          if ($location['ReceiptOrder'] <= 0) {
              continue;
          }


			$newData[] = $location;

      }	
      return $newData;
		
	}
	
}