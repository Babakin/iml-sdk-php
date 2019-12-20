<?php

namespace IMLSdk;

use IMLSdk\Guzzle;
use GuzzleHttp\Client;
use IMLSdk\Filters\FilterFactory;

/**
 * Class IMLClient
 * @package IMLSdk
 */
class IMLClient implements ICurlInject
{


    /**
     * Пароль аккаунта IML
     * @var string
     */
    private $login;

    /**
     * Логин аккаунта IML
     * @var string
     */
    private $password;

    /**
     * URL запроса, тестовый и боевой
     * @var string
     */
    public $baseUriActive;

    /**
     * Флаг тестового режима, когда активен, все запросы идут на тестовый сервер
     * @var bool
     */
    private $isTestMode = false;

    /**
     * Флаг авторизации пользователя в IML, по умолчанию не авторизован
     * @var bool
     */
    private $unauthorized = true;

    /**
     * @var ICurl
     */
    private $curl;


    /**
     * @var Order
     */
    private $order;

    /**
     * @var PointCollection
     */
    private $points;

    /**
     * @var ConditionCollection
     */
    private $conditions;

    /**
     * @var VatVariantsCollection
     */
    private $vatVariants;

    /**
     * @var CityCollection
     */
    private $cities;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * URL IML
     */
    const BASE_URI = 'http://api.iml.ru';

    /**
     * URL IML TEST
     */
    const BASE_URI_TEST = 'http://api-test.iml.ru';

    const BASE_URI_LIST = 'http://list.iml.ru';


    // timeout на время соединения с сервером API
    private $connectTimeout = 0;
    /**
     * IMLClient constructor.
     * @param Factory $factory
     * @throws ExceptionIMLClient
     */
    public function __construct(Factory $factory){
        $this->factory = $factory;
        $this->curl = $this->factory->getCurl();
        $this->points = $this->factory->getCollection('Point');
        $this->conditions = $this->factory->getCollection('Condition');
        $this->vatVariants = $this->factory->getCollection('Condition');
        $this->cities = $this->factory->getCollection('City');
        $this->baseUriActive = IMLClient::BASE_URI;
    }

    /**
     * Режим отладки, служебная информация о запросе будет выведена на экран
     * @param bool $debug
     */
    public function curlDebugMode(bool $debug): void{
        if($debug) $this->curl->debugMode();
        return;
    }

    /**
     * Проверка логина и пароля в службе IML
     * @param string $login
     * @param string $password
     * @return $this
     * @throws ExceptionIMLClient
     */
    public function logIn(string $login, string $password){
        $this->login = $login;
        $this->password = $password;
        // $this->checkAuth();
        return $this;
    }


    /**
     * DI Curl implements
     * @param ICurl $curl
     * @return mixed|void
     */
    public function injectCurl(ICurl $curl) :void {
        $this->curl = $curl;
        return;
    }

    /**
     * Активация тестового режима
     * @return $this
     */
    public function testMode() :IMLClient{
        $this->isTestMode = true;
        $this->baseUriActive = IMLClient::BASE_URI_TEST;
        return $this;
    }

    /**
     * Ставим timeout соединения в сек
     *
     * @param float $connectTimeout
     * @return void
     */
    public function setConnectTimeout(float $connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * Запрос к IML
     * @param string $uri
     * @param string $method
     * @param array $data
     * @param bool $listUri используется для получения данных из IML list
     * @param bool $convertResultFromJson Включить конвертирование ответа из json
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function request(string $uri, string $method = 'GET',array $data=[], bool $listUri = false, bool $convertResultFromJson = true) :IMLResponse
    {
        if(!$this->curl) 
        {
            throw new ExceptionIMLClient('Object ICUrl not found, use injectCurl method for injection');
        }
        if(!$this->login or !$this->password ) 
        {
            throw new ExceptionIMLClient('Логин или пароль отсутствуют, используйте метод logIn');
        }


        try{
            if($listUri)
            {
                return $this->curl->sendRequest(self::BASE_URI_LIST .'/'.$uri, $method, $this->login, $this->password, $data, $convertResultFromJson, $this->connectTimeout);
            }
            return $this->curl->sendRequest($this->baseUriActive.'/'.$uri, $method, $this->login, $this->password, $data, $convertResultFromJson, $this->connectTimeout);
        }catch (\Exception $exception){
            throw new ExceptionIMLClient('Ошибка запроса Curl');
        }
    }
    
    /*
     * Запрос к IML
     * @param string $uri
     * @param string $method
     * @param array $data
     * @param bool $listUri используется для получения данных из IML list
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    private function requestListData(string $uri, string $method = 'GET',array $data=[]) :IMLResponse
    {
        if(!$this->curl) 
        {
            throw new ExceptionIMLClient('Object ICUrl not found, use injectCurl method for injection');
        }

        try{
            return $this->curl->sendNonAuthRequest(self::BASE_URI_LIST .'/'.$uri, $method, $data);
        }catch (\Exception $exception){
            throw new ExceptionIMLClient('Ошибка запроса Curl');
        }

    }
    /**
     * Проверка прользователя в системе IML
     * @return $this
     * @throws ExceptionIMLClient
     */
    private function checkAuth(){
        if($this->unauthorized){
            $response = $this->curl->sendRequest($this->baseUriActive.'/'.'v5/GetPrice', 'POST', $this->login, $this->password, ['weigth'=> 2.3]);
            if($response->getStatusCode() !== 401 && $response->getStatusCode() !== 500){
                $this->unauthorized = false;
            }
        }
        return $this;
    }

    /**
     * Была ли авторизация с данным пользователем
     * @return bool
     */
    public function isAuth(){
        if($this->unauthorized){
            return false;
        }else{
            return true;
        }
    }


    /**
     * @param string $job
     * @return Order
     * @throws ExceptionIMLClient
     */
    public function getOrderInstance(string $job=null) :Order{
        $order = $this->factory->getOrder();
        if ($job) return $order->create($job);
        return $order;
    }

    /**
     * @param Order $order
     * @throws ExceptionIMLClient
     * @return $this
     */
    public function setOrder(Order $order){
        $order->checkDestination();
        $this->order = $order;
        return $this;
    }

    /**
     * @return Package
     * @throws \Exception
     */
    public function getPackageInstance(){
        return $this->factory->getPackage();
    }

    /**
     * @return $this
     * @throws ExceptionIMLClient
     */
    private function checkOrder(){
        if(is_null($this->order))
        {
            throw new ExceptionIMLClient('Нет исходных данных');
        }

        if(empty($this->order->getGoodItems())) 
        {
            throw new ExceptionIMLClient('В заказе нет ни одного грузового места');
        }
            
        return $this;
    }

    /**
     * @param string $productName
     * @param float $weightLine
     * @param float $amountLine
     * @param float|null $statisticalValueLine
     * @return Item
     */
    public function getItem(string $productName,float $weightLine,float $amountLine, float $statisticalValueLine=null):Item{
        return $this->factory->getItem()->create($productName, $weightLine, $amountLine, $statisticalValueLine);
    }


    /**
     * Расчет стоимости доставки
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function calculate() :IMLResponse{
        // ___p($this->order->toArray());
        // die();
        return $this->sendOrder('v5/GetPlantCostOrder');
    }



    /**
     * Создание заказа в IML
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function createOrder():IMLResponse{
        if(!$this->order->getCustomerOrder()) 
        {
            $this->order->setCustomerOrder(rand( 10000 , 99999 ));
        }
        // ___p($this->order->toArray());
        return $this->sendOrder('/Json/CreateOrder');
    }


    /**
     * получение структуры с данными pdf-файла со штрих-кодами заказа 
     * @param string $imlBarCode штрих-код заказа
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function getOrderBarcodesInPdfFormat(string $imlBarCode):IMLResponse
    {
        if(!$imlBarCode)
        {
            throw new ExceptionIMLClient('У заказа не указан штрих-код');
        }

        $pdfData = $this->request("/Json/PrintBar?Barcode={$imlBarCode}", 'GET', [], false, false);
        return $pdfData;

    }



    /**
     * получение статуса заказа в IML
     * @param string $imlBarCode штрих-код заказа
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function getStatusOrder(string $imlBarCode) :IMLResponse
    {
        if(!$imlBarCode)
        {
            throw new ExceptionIMLClient('У заказа не указан штрих-код');
        }
        return $this->request('Json/GetStatuses','POST',['BarCode' => $imlBarCode]);
    }



    /**
     * @param string $uri
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    private function sendOrder(string $uri):IMLResponse
    {
        $this->checkOrder();
        return $this->request($uri,'POST',$this->order->toArray());
    }

    /**
     * Собираем условия выдачи в Order
     */
    private function addConditions():void 
    {
        $this->order->addConditions($this->conditions);
        return;
    }


    /**
     * @param string $ID
     * @return Point|null
     * @throws ExceptionIMLClient
     */
    public function getPointByID(string $ID) :Point{
        $points = $this->getDeliveryPointsCollection();
        foreach ($points as $point){
            if($point->getID() == $ID) 
            {
                return $point;   
            }
        }
        return null;
    }



    /**
     * @param string $Code
     * @return Point|null
     * @throws ExceptionIMLClient
     */
    public function getPointByCode(string $Code) :Point{
        $points = $this->getDeliveryPointsCollection();
        foreach ($points as $point){
            if($point->getCode() == $Code) 
            {
                return $point;   
            }
        }
        return null;
    }

    /**
     * @param string $code
     * @return PointCollection
     * @throws ExceptionIMLClient
     */
    public function getPointsByRegionCode(string $code) :PointCollection{
        $response =  $this->request('sd?RegionCode='.$code,'GET',[],true);
        return $this->buildCollection($response->getContent(),'Point');
    }






    /** 
     * Получим список ПВЗ 
     * @param string $sdType поле Type  в списке ПВЗ (1 - пункт выдачи, 12 - постамат)
     * @param string $RegionCode Код региона для ПВЗ
     * @return PointCollection
     * @throws ExceptionIMLClient
     */
    public function getDeliveryPointsCollection(string $sdType = null, string  $RegionCode =  null):PointCollection{

            $params = compact('sdType', 'RegionCode');
            $paramsStr = http_build_query($params);
            $requestStr = ($paramsStr) ? 'sd?'.$paramsStr : 'sd';
            // ___p($requestStr);
            $response =  $this->requestListData($requestStr,'GET',[]);
            // фильтруем некорректные пвз
            $resultData = (FilterFactory::create('pickpoints'))->filterCollection($response->getContent());
            return $this->buildCollection($resultData, 'Point')->sortByAddr();
    }
    

    /**
     * @return PointCollection
     * @throws ExceptionIMLClient
     */
    public function getSortedDeliveryPointsCollection($sdType = null, $RegionCode =  null):PointCollection
    {
        $dpCollection = $this->getDeliveryPointsCollection($sdType , $RegionCode);
        return $dpCollection->sortByAddr();
    }
    
    

    /**
     * @return ConditionCollection
     * @throws ExceptionIMLClient
     */
    public function getConditions(){
        if($this->conditions->isEmpty()){
            try{
                $response = $this->requestListData('Status?type=json', 'GET', []);
                $data = [];
                foreach ($response->getContent() as $key=>$condition){
                    if($condition['StatusType'] === 40 && $condition['Code'] !== 13000 && !empty($condition['Name']))
                    {
                        $data[] = $condition;
                    }
                }
                $this->conditions = $this->buildCollection($data,'Condition');
            }catch (\Exception $e){
                if($e instanceof ExceptionIMLClient) throw $e;
                throw new ExceptionIMLClient('Ошибка запроса к http://list.iml.ru/Status?type=json');
            }
        }
        return $this->conditions;
    }



    /**
     * запрос списка доступных значений НДС, чтобы указывать его для товаров Item (Используется для передачи в ОФД (тег ФФД 1199).
     *
     * @return ConditionCollection
     */
    public function getVatVariants():ConditionCollection
    {
        if($this->vatVariants->isEmpty()){
            try{
                $response = $this->requestListData('Status?type=json', 'GET', []);
                $data = [];
                foreach ($response->getContent() as $key => $vatItem){
                    if($vatItem['StatusType'] === 52)
                    {
                        $data[] = $vatItem;
                    }
                }
                $this->vatVariants = $this->buildCollection($data,'Condition');
            }catch (\Exception $e){
                if($e instanceof ExceptionIMLClient) throw $e;
                throw new ExceptionIMLClient('Ошибка запроса к http://list.iml.ru/Status?type=json');
            }
        }
        return $this->vatVariants;
    }


    /**
     * Получить список складов 
     *
     * @return LocationCollection
     */
    public function getLocationCollection():LocationCollection
    {
        $response = $this->requestListData('Location?type=json');
        $resultData = (FilterFactory::create('location'))->filterCollection($response->getContent());
        return $this->buildCollection($resultData,'Location');
    }

    /**
     * Получить список населенных пунктов для доставки курьером
     *
     * @return CityCollection
     */
    public function getRegionCityCollection():CityCollection
    {
        $response = $this->requestListData('RegionCity?type=json');
        return $this->buildCollection($response->getContent(),'City');
    }

    /**
     * Получить отстортированная список населенных пунктов для доставки курьером (сначала - столицы)
     *
     * @return CityCollection
     */
    public function getSortedRegionCityCollection():CityCollection
    {
        $collection  = $this->getRegionCityCollection();
        return $collection->sortByCity();
    }


    /**
     * Поиск в списке доставки курьером по городу $city
     * В полученном объекте поле RegionIML
     * @param string $city 
     * @return CityCollection Все пункты, где встречается город
     * @throws ExceptionIMLClient
     */
    public function getRegionByCity(string $city):CityCollection
    {
        $response = $this->requestListData('RegionCity?type=json', 'GET', []);
        $shortest = 2;
        $result = [];
        $accurateResult = [];
        foreach ($response->getContent() as $reg){
            $levCity = levenshtein(mb_strtolower($city), mb_strtolower($reg['City']));
            if ($lev == 0) {
                $accurateResult[] = $reg;
            }

            if ($lev <= $shortest ) {
                $result[] = $reg;
            }
        }
        if(count($accurateResult)>0){
            return $this->buildCollection($accurateResult,'City');
        }
        return $this->buildCollection($result,'City');
    }
    
    
	/**
	 * Очистка названий города или региона от ненужных для поиска фрагментов и перевод в верхний регистр
	 *
	 * @param [string] $placeName
	 * @return string
	 */    
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

    /**
     * Поиск в списке доставки курьером по городу $city и региону
     * В полученном объекте поле RegionIML
     * @param string $city 
     * @param string $region 
     * @return CityCollection Все пункты, где встречается город
     * @throws ExceptionIMLClient
     */
    public function getRegionByCityRegion(string $city, string $region):CityCollection
    {
        $response = $this->requestListData('RegionCity?type=json', 'GET', []);
        $shortest = 2;
        $result = [];
        $accurateResult = [];
        // $arr = [['City' => 'Ханты-Мансийск г.', 
        // 'Region' => 'Ханты-Мансийский Автономный округ АО.']];
        //foreach ($arr as $reg)
        foreach ($response->getContent() as $reg)
        {
            $levCity = levenshtein($this->clearPlaceName($city), $this->clearPlaceName($reg['City']));
            $levRegion = levenshtein($this->clearPlaceName($region), $this->clearPlaceName($reg['Region']));

            if ($levCity == 0 && $levRegion == 0) {
                $accurateResult[] = $reg;
            }

            if ($levCity <= $shortest && $levRegion <= $shortest) {
                $result[] = $reg;
            }
        }
        
        if(count($accurateResult)>0){
            return $this->buildCollection($accurateResult,'City');
        }
        
        return $this->buildCollection($result,'City');
    }    


    /**
     * @param array $data
     * @param string $type
     * @return Collection
     * @throws ExceptionIMLClient
     */
    private function buildCollection(array $data,string $type){
        $collection = $this->factory->getCollection($type);
        $factoryMethod = 'get'.$type;
        foreach ($data as $cities){
            $collection->add($this->factory->$factoryMethod()->init($cities));
        }
        return $collection;
    }
}
