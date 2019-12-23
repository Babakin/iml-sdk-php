<?php


namespace IMLSdk;


class Item
{
    use ObjectToArrayTrait;
    use SplitStringCamesCase;

    /**
     * Наименование позиции
     * @var string
     */
    protected $productName;

    /**
     * Вес вложения
     * @var float
     */
    protected $weightLine;

    /**
     * Стоимость передаваемого вложения за единицу,
     * которую нужно взять с Покупателя,
     * включая НДС, с учетом всех скидок и надбавок.
     * @var float
     */
    protected $amountLine;

    /**
     * Оценочная стоимость.
     * Используется для компенсации в случаях повреждения или утери.
     * А также для отображения в документах первичной отчетности,
     * если Принципал выбрал варианты отчетов с подробностями.
     * @var float
     */
    protected $statisticalValueLine;

    /**
     * Номер вложения (артикул).
     * @var string
     */
    protected $productNo;

    /**
     * Вариант вложения (размер, цвет и т.д.)
     * @var string
     */
    protected $productVariant;

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
    protected $productBarCode;

    /**
     * Размер уже применённой к товару скидки.
     * Используется если Получателю необходимо передать размер скидки в фискальном чеке.
     * @var float
     */
    protected $discount;

    /**
     * Кол-во одинаковых вложений.
     * По умолчанию ставится значение = 1.
     * @var int
     */
    protected $itemQuantity = 1;

    /**
     * Запрет отказа от вложения при частичной выдаче заказа.
     * Если нужно чтобы Получатель не мог отказаться от данной
     * позиции при частичной выдаче, то заполнить значением 1.
     * @var bool
     */
    protected $deliveryService;

    /**
     * Служебное поле. Означает тип подробности - товарное вложение.
     * @var int
     */
    protected $itemType = 0;

    /**
     * Дополнительная информация, которую нужно принять к сведенью при выполнении услуги.
     * @var string
     */
    protected $itemNote;


    /**
     * размер НДС в процентах.Может принимать значения указанные в справочнике http://list.iml.ru/Status с типом 52. Используется для передачи в ОФД
     * @var int
     */
    protected $VATRate;


    /**
     * Item constructor.
     * @param string $productName наименование товара используется для передачи в ОФД (тег ФФД №1030)
     * @param float $weightLine вес товара
     * @param float $amountLine  стоимость товара используется для передачи в ОФД (тег ФФД 1043, 1079)
     * @param float $statisticalValueLine оценочная стоимость
     * @param string $ProductVariant   вариант товара (размер, цвет и т.д)
     * @param int $itemType тип подробности заказа (Товар, Услуга, Сопроводительные документы, Дополнительные условия выдачи заказа), используйте http://list.iml.ru/Status c типом 39, чтобы понять какие значения может принимать это поле
     * @param int $productNo номер товара (артикул)
     * @param int $productBarCode штрих-код
     * @return Item
     */
    public function __construct(string $productName,float $weightLine,float $amountLine, float $statisticalValueLine = null,
        string $ProductVariant = null, int $itemType = 0, int $productNo = null, int $productBarCode = null
    ){
        $this->ProductVariant = $ProductVariant;
        $this->itemType = $itemType;  
        $this->productName = $productName;
        $this->weightLine = $weightLine;
        $this->amountLine = $amountLine;
        $this->statisticalValueLine = $statisticalValueLine;
        $this->productNo = (is_null($productNo)) ? rand(10000, 99999) : $productNo;
        $this->productBarCode = (is_null($productBarCode)) ? rand (10000000, 99999999) : $productBarCode;
        return $this;
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
        $prop = $this->stringSplitCamelCase($method,'set');
        if(!property_exists($this,$prop)) 
        {
            throw new ExceptionIMLClient('Неверное имя свойства');
        }
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
     * Наложенная стоимость
     * @return float
     */
    public function getStatisticalValueLine(){
        return $this->statisticalValueLine;
    }

    /**
     * код категории НДС
     * @return void
     */    

    public function setVATRate(int $VATRate)
    {
        $this->VATRate = $VATRate;
    }
}
