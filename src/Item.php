<?php


namespace IMLSdk;


class Item
{
    use ObjectToArrayTrait;

    /**
     * Наименование позиции
     * @var string
     */
    private $productName;

    /**
     * Вес вложения
     * @var float
     */
    private $weightLine;

    /**
     * Стоимость передаваемого вложения за единицу,
     * которую нужно взять с Покупателя,
     * включая НДС, с учетом всех скидок и надбавок.
     * @var float
     */
    private $amountLine;

    /**
     * Оценочная стоимость.
     * Используется для компенсации в случаях повреждения или утери.
     * А также для отображения в документах первичной отчетности,
     * если Принципал выбрал варианты отчетов с подробностями.
     * @var float
     */
    private $statisticalValueLine;

    /**
     * Номер вложения (артикул).
     * @var string
     */
    private $productNo;

    /**
     * Вариант вложения (размер, цвет и т.д.)
     * @var string
     */
    private $productVariant;

    /**
     * Штрих код вложения. Вложения в заказ должны быть промаркированы этикетками,
     * содержащими штрих-код. Длинна штрих-кода должна быть больше 4 и не более 50 символов.
     * Если товар производится самим отправителем или производитель не нанес маркировку,
     * то отправитель обязан сам выполнить данное требование. Используется в случае
     * частичной выдачи заказа Получателю. Если будем указано неверное или вымышленное значение,
     * то курьер или оператор ПВЗ не сможет оформить заказ.
     * Также используется для подбора заказа в случае ответственного хранения или комплектации
     * без ответственного хранения (кроссдокинг) на складах IML.
     * Также это поле используется в других типах строк для идентификационных целей.
     * Исключение: если отправитель заблокировал услугу частичной выдачи или не использует услугу комплектации заказа.
     * @var string
     */
    private $productBarCode;

    /**
     * Размер уже применённой к товару скидки.
     * Используется если Получателю необходимо передать размер скидки в фискальном чеке.
     * @var float
     */
    private $discount;

    /**
     * Кол-во одинаковых вложений.
     * По умолчанию ставится значение = 1.
     * @var int
     */
    private $itemQuantity = 1;

    /**
     * Запрет отказа от вложения при частичной выдаче заказа.
     * Если нужно чтобы Получатель не мог отказаться от данной
     * позиции при частичной выдаче, то заполнить значением 1.
     * @var bool
     */
    private $deliveryService;

    /**
     * Служебное поле. Означает тип подробности - товарное вложение.
     * @var int
     */
    private $itemType = 0;

    /**
     * Дополнительная информация, которую нужно принять к сведенью при выполнении услуги.
     * @var string
     */
    private $itemNote;


    /**
     * Item constructor.
     * @param string $productName
     * @param float $weightLine
     * @param float $amountLine
     * @param float $statisticalValueLine
     */
    public function __construct(string $productName,float $weightLine,float $amountLine, float $statisticalValueLine=null){
        $this->productName = $productName;
        $this->weightLine = $weightLine;
        $this->amountLine = $amountLine;
        $this->statisticalValueLine = $statisticalValueLine;
        $this->productNo = rand (10000, 99999);
        $this->productBarCode = rand (10000000, 99999999);
    }

    /**
     * Для назначения свойства объекта используйте set[Название свойства CamelCase].
     * Метод перехватывает определение свойства.
     * @param $method
     * @param $args
     * @return $this
     * @throws ExceptionIMLClient
     */
    public function __call($method, $args) {
        if (count($args)>1) throw new ExceptionIMLClient('Неверные параметры для свойства');
        $arr = preg_split('/(?=[A-Z])/',$method);
        if($arr[0] == 'set'){
            unset($arr[0]);
            $prop = lcfirst(implode ($arr));
        }
        if(!property_exists($this,$prop)) throw new ExceptionIMLClient('Неверное имя свойства');

        $this->$prop = $args[0];
        return $this;
    }

    /**
     * Вернет количество товаров данного вида.
     * @return int
     */
    public function getQuantity(){
        return $this->itemQuantity;
    }

    /**
     * Вес товарного вложения
     * @return float
     */
    public function getWeightLine(){
        return $this->weightLine;
    }

    /**
     * Стоимость товарного вложения
     * @return float
     */
    public function getAmount(){
        return $this->amountLine;
    }

    /**
     * Наложенная стоисомть
     * @return float
     */
    public function getStatisticalValueLine(){
        return $this->statisticalValueLine;
    }
}
