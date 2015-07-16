<?php
/**
 * CollectionException.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace pillow\src\Exception;


use Framework\Collection\BasicCollection;

class CollectionException extends \Exception
{
    /** @var  BasicCollection $errors */
    protected $errors;
    protected $realErrorCode = null;

    public function __construct(Array $errors = null)
    {
        $this->errors = new BasicCollection($errors);
    }

    /**
     * @return BasicCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function __toString()
    {
        $errorStr = '';
        foreach($this->getErrors() as $error){
            $errorStr .= (string)$error.PHP_EOL;
        }
    }


}