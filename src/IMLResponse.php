<?php


namespace IMLSdk;


class IMLResponse
{

    /**
     * @var string
     */
    public $title;

    /**
     * @var int
     */
    public $statusCode;

    /**
     * @var array
     */
    public $content;

    /**
     * IMLResponse constructor.
     * @param string $title
     * @param int $statusCode
     * @param $content
     */
    public function __construct(string $title, int $statusCode, $content=null){
        $this->title = $title;
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    /**
     * @return mixed
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getStatusCode(){
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getContent(){
        return $this->content;
    }

    /**
     * Если в теле ответа IML есть ошибки, вернет строку с ними.
     * @return string
     */
    public function getErrorsMessage() :string {
        if($this->content['Errors']){
            $message = '';
            foreach ($this->content['Errors'] as $error){
                $message .= ' '.$error->ErrorMessage;
            }
            return $message;
        }
    }
}
