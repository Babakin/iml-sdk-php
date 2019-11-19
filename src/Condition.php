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
    private $productNo;

    /**
     * Если указано значение 1, то условие выдачи разрешено.
     * Если указано значение 0 – запрещено.
     * @var bool
     */
    private $allowed = 1;

    /**
     * Для определения условий выдачи всегда заполнять значением ‘10’.
     * @var int
     */
    private $itemType = 10;

    /**
     * Дополнительная информация, которую нужно принять к сведенью.
     * @var string
     */
    private $itemNote;

    /**
     * Имя условия выдачи
     * @var string
     */
    private $name;

    /**
     * Описание типа выдачи
     * @var string
     */
    private $statusTypeDescription;

    /**
     * Описание
     * @var string
     */
    private $description;

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
     * @param bool $allowed
     * @return $this
     */
    public function allowed(bool $allowed){
        $this->allowed = (int) $allowed;
        return $this;
    }
    
    
    public function __get($property)
    {
        
        if (property_exists($this, $property)) 
        {
            return $this->$property;
        }
        
    }
    
}
