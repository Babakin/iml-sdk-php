<?php


namespace IMLSdk;


class Point
{
    public $ID;
    public $CalendarWorkCode;
    public $Code;
    public $Name;
    public $RequestCode;
    public $RegionCode;
    public $Index;
    public $Address;
    public $Phone;
    public $EMail;
    public $WorkMode;
    public $FittingRoom;
    public $PaymentCard;
    public $PaymentPossible;
    public $ReceiptOrder;
    public $Latitude;
    public $Longitude;
    public $HomePage;
    public $ClosingDate;
    public $OpeningDate;
    public $CouponReceipt;
    public $DaysFreeStorage;
    public $SubAgent;
    public $DeliveryTimeFrom;
    public $DeliveryTimeTo;
    public $Carrier;
    public $ReplicationPath;
    public $Submission;
    public $Special_Code;
    public $HowToGet;
    public $FormPostCode;
    public $FormRegion;
    public $FormCity;
    public $FormStreet;
    public $FormHouse;
    public $FormBuilding;
    public $FormOffice;
    public $FormKLADRCode;
    public $FormFIASCode;
    public $FormalizedArea;
    public $FormalizedLocality;
    public $Scale;
    public $TimeZone;
    public $Type;
    public $ReplacementLocation;

    /**
     * @param $data
     * @return Point
     */
    public static function buildPoint($data) :Point{
        $point = new self;
        $data = (array)$data;
        $keys = array_keys($data);
        foreach (get_object_vars($point) as $prop=>$val){
            if(in_array($prop,$keys)){
                $point->$prop = $data[$prop];
            }
        }
        return $point;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set( $key, $value )
    {
        if (isset($this->$key)) {
            $this->$key = $value;
        }
    }
}
