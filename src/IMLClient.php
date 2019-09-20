<?php

namespace IMLSdk;

use IMLSdk\Guzzle;
use GuzzleHttp\Client;

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
    public $login;

    /**
     * Логин аккаунта IML
     * @var string
     */
    public $password;

    /**
     * URL запроса, тестовый и боевой
     * @var string
     */
    public $baseUriActive;

    /**
     * Флаг тестового режима, когда активен, все запросы идут на тестовый сервер
     * @var bool
     */
    public $isTestMode = false;

    /**
     * Флаг авторизации пользователя в IML, по умолчанию не авторизован
     * @var bool
     */
    public $unauthorized = true;

    /**
     * @var ICurl
     */
    public $curl;

    /**
     * @var Container
     */
    public $container;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var Point[]
     */
    public $points;

    /**
     * @var Condition[]
     */
    public $conditions;

    /**
     * URL IML
     */
    const BASE_URI = 'http://api.iml.ru';

    /**
     * URL IML TEST
     */
    const BASE_URI_TEST = 'http://api-test.iml.ru';

    /**
     * IMLClient constructor.
     * @param integer $login
     * @param string $password
     * @param Container $container
     * @param bool $test
     * @param bool $curlDebug
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(string $login, string $password, Container $container, bool $test=false,$curlDebug = false){
        $this->testMode($test);
        $this->login = $login;
        $this->password = $password;
        $this->container = $container;
        $this->setCurl($this->container->getCurl(),$curlDebug);
        $this->auth();
    }

    /**
     * Инициализация клиента
     * @param string $login
     * @param string $password
     * @param bool $test
     * @param bool $curlDebug
     * @return IMLClient
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function instance(string $login, string $password, bool $test = false,$curlDebug = false){
        return new static($login,$password, new Container(), $test,$curlDebug);
    }

    /**
     * DI Curl implements
     * @param ICurl $curl
     * @param bool $debug
     * @return mixed|void
     */
    public function setCurl(ICurl $curl,bool $debug = false){
        $this->curl = $curl;
        if($debug) $this->curl->debug();
    }

    /**
     * Активация тестового режима
     * @param bool $active
     * @return $this
     */
    private function testMode($active){

        if($active){
            $this->isTestMode = $active;
            $this->baseUriActive = IMLClient::BASE_URI_TEST;
        }else{
            $this->isTestMode = $active;
            $this->baseUriActive = IMLClient::BASE_URI;
        }
        return $this;
    }

    /**
     * Запрос к IML
     * @param string $uri
     * @param string $method
     * @param array $data
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws ExceptionIMLClient
     */
    public function request(string $uri, string $method = 'GET',array $data=[]) :IMLResponse{
        try{
            return $this->curl->sendRequest($this->baseUriActive.'/'.$uri, $method, $this->login, $this->password, $data);
        }catch (\Exception $exception){
            throw new ExceptionIMLClient('Ошибка запроса Curl');
        }
    }

    /**
     * Проверка прользователя в системе IML
     * @return $this
     * @throws ExceptionIMLClient
     */
    public function auth(){
        if($this->unauthorized){
            $response = $this->request('v5/GetPrice','POST', ['weigth'=>2.3]);
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
     * @return $this
     * @throws ExceptionIMLClient
     */
    private function checkOrder(){
        if(is_null($this->order)) throw new ExceptionIMLClient('Нет исходных данных');
        return $this;
    }


    /**
     * Расчет стоимости доставки
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function calculate() :IMLResponse{
        return $this->sendOrder('v5/GetPlantCostOrder');
    }

    /**
     * Создание заказа в IML
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    public function createOrder():IMLResponse{
        if(!$this->order->customerOrder) $this->order->setCustomerOrder(rand ( 10000 , 99999 ));
        return $this->sendOrder('/Json/CreateOrder');
    }

    /**
     * @param string $uri
     * @return IMLResponse
     * @throws ExceptionIMLClient
     */
    private function sendOrder(string $uri):IMLResponse{
        $this->checkOrder();
        $this->getConditions();
        $this->prepareConditions();
        return $this->request($uri,'POST',$this->order->toArray());
    }

    /**
     * Собираем условия выдачи в Order
     */
    private function prepareConditions(){
        foreach ($this->conditions as $condition){
            $this->order->goodItems = array_merge($this->order->goodItems,[$condition->toArray()]);
        }
        return;
    }

    /**
     * @param string $Code
     * @return Point|null
     * @throws ExceptionIMLClient
     */
    public function getPointByCode(string $Code) :?Point{
        $points = $this->getDeliveryPoints();
        foreach ($points as $point){
            if($point->Code == $Code) return $point;
        }
        return null;
    }

    /**
     * @return array|Point[]
     * @throws ExceptionIMLClient
     */
    public function getDeliveryPoints(){
        if(!$this->points){
            $response =  $this->request('list/sd');
            $points = [];
            foreach ($response->getContent() as $point){
                $points[] = $this->container->getPoint($point);
            }
            $this->points =  $points;
        }
        return $this->points;
    }

    /**
     * @return $this
     * @throws ExceptionIMLClient
     */
    public function getConditions(){
        if(!$this->conditions){
            try{
                $response = $this->curl->sendRequest('http://list.iml.ru/Status?type=json', 'GET', $this->login, $this->password);
            }catch (\Exception $exception){
                throw new ExceptionIMLClient('Ошибка запроса к http://list.iml.ru/Status?type=json');
            }
            $conditions = [];
            foreach ($response->getContent() as $condition){
                if($condition->StatusType == 40 && $condition->Code !== 13000){
                    $conditions[] = $this->container->getCondition($condition);
                }
            }
            $this->conditions =  $conditions;
        }
        return $this;
    }
}
