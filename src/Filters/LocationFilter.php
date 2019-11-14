<?php 

namespace IMLSdk\Filters;

class LocationFilter extends Filter
{

	public function filterCollection():array
	{
		$newData = [];
      	foreach ($this->collection as $location) {

          $upperRegionCode = mb_strtoupper($location['RegionCode'], 'UTF-8');

          if (!empty($location['OpeningDate']) && strtotime($location['OpeningDate']) >= time()) {
              continue;
          }

          if ($location['RegionCode'] == 'ПОЧТА') {
              continue;
          }

          //if ($location ['Submission'] != '')
          //continue;

          if ($location['ReceiptOrder'] <= 0) {
              continue;
          }


			$newData[] = $location;

      }	
      return $newData;
		
	}
	
}