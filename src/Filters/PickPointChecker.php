<?php

namespace IMLSdk\Filters;

class PickPointChecker
{

    private $federalCities;

    function __construct($federalCities)
    {
        $this->federalCities = $federalCities;
    }


    public function isCorrectPickpoint(&$pickpoint)
    {
        $restrictedIDs = ['1001454', '1000607'];

        if(in_array($pickpoint['ID'], $restrictedIDs))
        {
            return false;
        }

        $RegionCode = isset($pickpoint['RegionCode']) ? $pickpoint['RegionCode'] : '';

        $upperFormRegion    = mb_strtoupper($pickpoint['FormRegion'], 'UTF-8');
        $upperFormCity      = mb_strtoupper($pickpoint['FormCity'], 'UTF-8');
        // $upperFormalizedLocality      = mb_strtoupper($pickpoint['FormalizedLocality'], 'UTF-8');

        $IsCorrectPlaceData = (!empty($upperFormCity) && !empty($upperFormRegion))
            || (empty($upperFormCity) && in_array($upperFormRegion, $this->federalCities))
            || (empty($upperFormRegion) && in_array($upperFormCity, $this->federalCities));


        // переводим FormalizedLocality в FormCity
        if(!$IsCorrectPlaceData && !empty($pickpoint['FormalizedLocality']) && empty($upperFormCity) &&
            !empty($upperFormRegion))
        {
            $pickpoint['FormCity'] = $pickpoint['FormalizedLocality'];
            $pickpoint['FormalizedLocality'] = '';
            $IsCorrectPlaceData = true;
        }

        $ReceiptOrder = isset($pickpoint['ReceiptOrder']) ? $pickpoint['ReceiptOrder'] : '';
        $Code         = isset($pickpoint['Code']) ? $pickpoint['Code'] : '';

        if (!$RegionCode || $RegionCode == 'ПОЧТА' || !$IsCorrectPlaceData || !$Code || $ReceiptOrder === '') {
            return false;
        }

        if (!empty($pickpoint['OpeningDate']) && strtotime($pickpoint['OpeningDate']) >= time()) {
            return false;
        }

        return true;
    }


    public function isDepartureC2C($pickpoint)
    {
        return $pickpoint['ReceiptOrder'] == 2 || $pickpoint['ReceiptOrder'] == 7;
    }

}
