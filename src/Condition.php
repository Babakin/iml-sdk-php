<?php


namespace IMLSdk;


use function GuzzleHttp\Psr7\str;

/**
 * Class Condition
 * @package IMLSdk
 */
class Condition
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
    private $allowed;

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
     * @param object $data
     * @return Condition
     */
    public static function buildCondition(object $data) :Condition{
        $condition = new self;
        $condition->productNo = $data->Code;
        $condition->allowed = false;
        $condition->name = $data->Name;
        $condition->statusTypeDescription = $data->StatusTypeDescription;
        $condition->description = $data->Description;
        return $condition;
    }

    /**
     * Дополнительный комментарий к условию выдачи
     * @param string $note
     * @return $this
     */
    public function setItemNote(string $note){
        $this->itemNote = $note;
        return $this;
    }
}
