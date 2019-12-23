<?php


namespace IMLSdk;


class Point extends BaseObject
{
    use SplitStringCamesCase;

    protected $ID;
    protected $CalendarWorkCode;
    protected $Code;
    protected $Name;
    protected $RequestCode;
    protected $RegionCode;
    protected $Index;
    protected $Address;
    protected $Phone;
    protected $EMail;
    protected $WorkMode;
    protected $FittingRoom;
    protected $PaymentCard;
    protected $PaymentPossible;
    protected $PaymentType;
    protected $ReceiptOrder;
    protected $Latitude;
    protected $Longitude;
    protected $HomePage;
    protected $ClosingDate;
    protected $OpeningDate;
    protected $CouponReceipt;
    protected $DaysFreeStorage;
    protected $SubAgent;
    protected $DeliveryTimeFrom;
    protected $DeliveryTimeTo;
    protected $Carrier;
    protected $ReplicationPath;
    protected $Submission;
    protected $Special_Code;
    protected $HowToGet;
    protected $FormPostCode;
    protected $FormRegion;
    protected $FormCity;
    protected $FormStreet;
    protected $FormHouse;
    protected $FormBuilding;
    protected $FormOffice;
    protected $FormKLADRCode;
    protected $FormFIASCode;
    protected $FormalizedArea;
    protected $FormalizedLocality;
    protected $Scale;
    protected $TimeZone;
    protected $Type;
    protected $ReplacementLocation;


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

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws ExceptionIMLClient
     */
    public function __call($name, $arguments){
        $prop = $this->stringSplitCamelCase($name,'get');
        $prop = ucfirst($prop);
        if(!property_exists($this,$prop)) throw new ExceptionIMLClient('Неверное имя свойства');
        return $this->$prop;
    }
    
    /**
     * получение строки адреса на основе Form-полей
     *
     * @param array $excludes Массив Form-полей, исключаемых при формировании строки адреса ПВЗ
     * @return string
     */
    public function getFormAddress(array $excludes = []):string
    {
        $params = [
        'FormRegion',
        'FormalizedLocality',
        'FormalizedArea',
        'FormCity',
        'FormStreet',
        'FormHouse',
        'FormBuilding',
        'FormOffice'];
        
        $formatAr = [];
        foreach ($params as $param) {
            if(!in_array($param, $excludes) && $this->$param)
            {
                $formatAr[] = $this->$param;    
            }
        }
    
    
        return implode(', ', $formatAr);
    }
}
