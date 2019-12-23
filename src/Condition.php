<?php


namespace IMLSdk;


use function GuzzleHttp\Psr7\str;

/**
 * Class Condition
 * @package IMLSdk
 */
class Condition extends BaseObject
{
    use ObjectToArrayTrait;

    /**
     * Может принимать значения только из справочника
     * расположенного на справочном сервисе
     * IML http://list.iml.ru/ExportToExcel?table=Status
     * поля с StatusType = 40
     * @var int
     */
    protected $productNo;

    /**
     * Если указано значение 1, то условие выдачи разрешено.
     * Если указано значение 0 – запрещено.
     * @var bool
     */
    protected $allowed = 1;

    /**
     * Для определения условий выдачи всегда заполнять значением ‘10’.
     * @var int
     */
    protected $itemType = 10;

    /**
     * Дополнительная информация, которую нужно принять к сведенью.
     * @var string
     */
    protected $itemNote;

    /**
     * Имя условия выдачи
     * @var string
     */
    protected $name;

    /**
     * Описание типа выдачи
     * @var string
     */
    protected $statusTypeDescription;

    /**
     * Описание
     * @var string
     */
    protected $description;

    /**
     * Создание обьекта по параметрам, полученным из api IML
     * @param array $data
     * @return Condition
     */
    public function init(array $data) :BaseObject{
        $condition = new static();
        $condition->productNo = $data['Code'];
        $condition->allowed = true;
        $condition->name = $data['Name'];
        $condition->statusTypeDescription = $data['StatusTypeDescription'];
        $condition->description = $data['Description'];
        return $condition;
    }

    /**
     * Дополнительный комментарий к условию выдачи
     * @param string $note
     * @return $this
     */
    public function setItemNote(string $note) :Condition{
        $this->itemNote = $note;
        return $this;
    }


    /**
     * Разрешено ли свойство в данном заказе
     * @param bool $allowed
     * @return $this
     */
    public function allowed(bool $allowed){
        $this->allowed = (int) $allowed;
        return $this;
    }
        
}
