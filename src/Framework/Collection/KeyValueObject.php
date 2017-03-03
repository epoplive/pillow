<?php
/**
 * KeyValueObject.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Collection;


final class KeyValueObject
{
    private $key;
    private $value;

    /**
     * KeyValueObject constructor.
     * @param $key
     * @param $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}