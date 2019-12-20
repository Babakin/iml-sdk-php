<?php


namespace IMLSdk;

/**
 * Class Order
 * @package IMLSdk
 */
class Order
{
    use ObjectToArrayTrait;
    use SplitStringCamesCase;

    /**
     * штрих-код заказа, созданного в системе IML
     * @var string
     */
    protected $barcode;
    
    
    /**
     * Job – услуга, Code из справочника услуг, находится по адресу http://list.iml.ru/service
     * @var string
     */
    protected $job;

    /**
     * Номер заказа, любая строка, если не указан, передается случайное число
     * @var string
     */
    protected $customerOrder;

    /**
     * Вес(кг) Дробное число, указывается с разделетилем целой и дробной части - точкой.
     * @var float
     */
    protected $weight;

    /**
     * Кол-во мест в заказе, от 1 до 9
     * @var integer
     */
    protected $volume = 0;

    /**
     * Тестовый режим, 'true' для тестового режима
     * @var bool
     */
    protected $test = false;

    /**
     * Стоимость ТМЦ
     * @var float
     */
    protected $amount = 0;

    /**
     * Наложенная стоимость
     * @var float
     */
    protected $valuatedAmount = 0;

    /**
     * Регион отправления. Code из таблицы регионов, находится по адресу http://list.iml.ru/region
     * @var string
     */
    protected $regionCodeFrom;

    /**
     * Регион получения. Code из таблицы регионов, находится по адресу http://list.iml.ru/region
     * @var string
     */
    protected $regionCodeTo;

    /**
     * Индекс региона отправления, альтернатива RegionCodeTo
     * @var integer
     */
    protected $indexFrom;

    /**
     * Индекс региона получения, альтернатива RegionCodeTo
     * @var integer
     */
    protected $indexTo;

    /**
     * RequestCode из таблицы пунктов самовывоза
     * @var string
     */
    protected $deliveryPoint;

    protected $address;

    /**
     * Уникальный номер пункта самовывоза, Code из таблицы пунктов самовывоза, альтернатива DeliveryPoint
     * @var string
     */
    protected $DeliveryPointCode;

    /**
     * Электронный адрес получателя заказа
     * @var string
     */
    protected $email;

    /**
     * Телефон
     * @var string
     */
    protected $phone;

    /**
     * Контактное лицо
     * @var string
     */
    protected $contact;

    /**
     * Комментарий к заказу
     * @var string
     */
    protected $comment;

    /**
     * Позиции заказа, если указывались при создании заказа
     * @var Item[]
     */
    protected $goodItems = [];

    /**
     * Возможные услуги для использования
     */
    const SERVICES = [self::SELF_DELIVERY,self::DELIVERY, self::DELIVERY_CASH_SERVICE, self::SELF_DELIVERY_CASH_SERVICE];

    /**
     * Доставка на ПВЗ предоплаченного заказа
     */
    const SELF_DELIVERY = 'С24';

    /**
     * Доставка на ПВЗ заказа с касс. обсл-ем
     */
    const SELF_DELIVERY_CASH_SERVICE = 'С24КО';

    /**
     * Доставка предоплаченого заказа
     */
    const DELIVERY = '24';

    /**
     * Доставка с кассовым обслуживанием
     */
    const DELIVERY_CASH_SERVICE = '24КО';


    /**
     * @param string $job
     * @throws ExceptionIMLClient
     * @return self
     */
    public function create(string $job) :Order {
        if(!in_array($job, self::SERVICES)){
            throw new ExceptionIMLClient('Услуги '.$job.' не существует, возможные услуги '.implode(',',self::SERVICES));
        }
        $this->job = $job;
        return $this;
    }

    /**
     * Пара почтовых индексов Из и Куда
     * @param int $from
     * @param int $to
     * @return $this
     */
    public function postIndexes(int $from, int $to){
        $this->indexTo($to);
        $this->indexFrom($from);
        return $this;
    }

    /**
     * @param string|null $phone
     * @param string|null $email
     * @param string|null $contactPerson
     * @return $this
     */
    public function setContacts(string $phone = null,string $email = null,string $contactPerson = null){
        if($phone) $this->setPhone($phone);
        if($email) $this->setEmail($email);
        if($contactPerson) $this->setContactPerson($contactPerson);
        return $this;
    }



    /**
     * @param string $barcode штрих-код заказа
     * @return $this
     */
    public function setBarcode(string $barcode){
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone){
        $this->phone = $phone;
        return $this;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email){
        $this->email = $email;
        return;
    }

    /**
     * @param string $contactPerson
     * @return $this
     */
    public function setContactPerson(string $contactPerson){
        $this->contact = $contactPerson;
        return $this;
    }

    /**
     * @param $customerOrder
     * @return $this
     */
    public function setCustomerOrder($customerOrder){
        $this->customerOrder = $customerOrder;
        return $this;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment(string $comment){
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param int $to
     * @return $this
     */
    public function indexTo(int $to){
        $this->indexTo = $to;
        return $this;
    }

    /**
     * @param int $from
     * @return $this
     */
    public function indexFrom(int $from){
        $this->indexFrom = $from;
        return $this;
    }

    /**
     * Коды регионов из справочника IML
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function regionCodes(string $from, string $to=''){
        $this->regionFrom($from);
        $this->regionTo($to);
        return $this;
    }

    /**
     * @param string $regionTo
     * @return $this
     */
    public function regionTo(string $regionTo){
        $this->regionCodeTo = $regionTo;
        return $this;
    }

    /**
     * @param string $from
     * @return $this
     */
    public function regionFrom(string $from){
        $this->regionCodeFrom = $from;
        return $this;
    }

    /**
     * @param string $requestCode
     * @return $this
     */
    public function setDeliveryPoint(string $requestCode){
        $this->deliveryPoint = $requestCode;
        return $this;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddressDelivery(string $address){
        $this->address = $address;
        return $this;
    }

    /**
     * @return $this
     * @throws ExceptionIMLClient
     */
    public function checkDestination(){
        $check = true;
        if($this->job == self::DELIVERY || $this->job == self::DELIVERY_CASH_SERVICE){
            if($this->indexFrom){
                if(empty($this->indexTo)) $check = false;
            }
            if($this->regionCodeFrom){
                if(empty($this->regionCodeTo)) $check = false;
            }
        }
        if(!$check) throw new ExceptionIMLClient('Регион назначения обязателен для услуги '.$this->job);

        if($this->job == self::SELF_DELIVERY_CASH_SERVICE || $this->job == self::SELF_DELIVERY){
            if(!$this->DeliveryPointCode) throw new ExceptionIMLClient('Нет пункта назначения самовывоза для услуги '.$this->job);
        }
        return $this;
    }

    /**
     * @param Point $pointTo
     * @return $this
     */
    public function setPointTo(Point $pointTo){
        $this->setDeliveryPointCode($pointTo->getCode());
        $this->setAddressDelivery($pointTo->getAddress());
        $this->regionTo($pointTo->getRegionCode());
        $this->setDeliveryPoint($pointTo->getRequestCode());
        return $this;
    }

    /**
     * @param Package $package
     * @return $this
     */
    public function setPackage(Package $package){
        $this->volume++;
        $this->goodItems = array_merge($this->goodItems,[$package->getPackage()]);
        $this->weight += $package->getWeightLine();
        $this->setItems($package->getItems());
        return $this;
    }

    /**
     * @param array $items
     */
    private function setItems(array $items){
        /** @var Item $item **/
        foreach ($items as $item){
            $this->setItem($item);
            // если тип доставки связан с кассовым обслуживанием - заполнять Amount
            if(in_array($this->job, [self::SELF_DELIVERY_CASH_SERVICE, self::DELIVERY_CASH_SERVICE]))
            {
                $this->amount += $item->getAmount()*$item->getQuantity();    
            }
            $this->valuatedAmount += $item->getStatisticalValueLine()*$item->getQuantity();
        }
    }

    /**
     * Добавить условия выдачи к заказу
     *
     * @param ConditionCollection $conditions коллекция условий выдачи
     * @return void
     */
    public function addConditions(ConditionCollection $conditions) {
        foreach ($conditions as $condition){
            $this->goodItems = array_merge($this->goodItems,[$condition->toArray()]);
        }
    }

    /**
     * @param Item $item
     */
    private function setItem(Item $item){
        $this->goodItems = array_merge($this->goodItems,[$item->toArray()]);
    }


    /**
     * @return $this
     */
    public function testMode(){
        $this->test = true;
        return $this;
    }


    /**
     * Добавляем услугу "Доставка" в заказ с указанием стоимости
     * @param float $DeliveryCost Стоимость доставки
     * @return void
     */
    public function setDeliveryCost(float $DeliveryCost)
    {
        $this->goodItems = array_merge($this->goodItems, ["productNo" => "Доставка", "itemType" => 3, "allowed" => 0, 'amountLine' => $DeliveryCost]);
    }



    /**
     * Включаем фулфилмент в заказе
     *
     * @return void
     */
    public function enableFullFilment()
    {
        $this->goodItems = array_merge($this->goodItems, ["productNo" => "10000", "itemType" => "14", "allowed" => "1", "productBarCode" => "00000"]);
    }



    /**
     * Указываем пункт выдачи для заказа
     * @param string $Code
     * @return $this
     */
    public function setDeliveryPointCode(string $Code){
        $this->DeliveryPointCode = $Code;
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws ExceptionIMLClient
     */
    public function __call($name, $arguments){
        $prop = $this->stringSplitCamelCase($name,'get');
        if(!property_exists($this,$prop)) 
            {
                throw new ExceptionIMLClient('Неверное имя свойства');
            }
        return $this->$prop;
    }
}
