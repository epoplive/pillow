<?php
/**
 * BasicCollection.php
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

class KeyValueCollection extends BasicCollection implements CollectionInterface {
    public function add($element){
        if(!$element instanceof KeyValueObject){
            throw new \InvalidArgumentException("\$element must of type ".KeyValueObject::class);
        }
        parent::add($element);
    }

    public function insert($index, $element){
        if(!$element instanceof KeyValueObject){
            throw new \InvalidArgumentException("\$element must of type ".KeyValueObject::class);
        }
        parent::insert($index, $element);
    }

    public function findIndexByKey($key, $offset = 0){
        if((int)$offset < 0){
            throw new \InvalidArgumentException(__METHOD__.": \$offset must not be less than 0!");
        }
        $findCount = 0;
        foreach($this->_collection as $index => $value){
            /** @var KeyValueObject $value */
            if($value->getKey() === $key){
                if(++$findCount > $offset){
                    return $index;
                }
            }
        }
        return false;
    }

    public function findIndexByValue($value, $offset = 0){
        if((int)$offset < 0){
            throw new \InvalidArgumentException(__METHOD__.": \$offset must not be less than 0!");
        }
        $findCount = 0;
        foreach($this->_collection as $index => $val){
            /** @var KeyValueObject $val */
            if($val->getValue() === $value){
                if(++$findCount > $offset){
                    return $index;
                }
            }
        }
        return false;
    }

    public function getByKey($key)
    {
        if(($index = $this->findIndexByKey($key)) === false){
            return false;
        }
        return $this->_collection[$index];
    }

    public function getByValue($value)
    {
        $index = $this->findIndexByValue($value);
        return $this->_collection[$index];
    }

    public function removeByKey($key){
        foreach($this->_collection as $index => $value){
            /** @var KeyValueObject $value */
            if($value->getKey() === $key){
                $this->removeAtIndex($index);
            }
        }
    }

    public function removeByValue($value){
        foreach($this->_collection as $index => $val){
            /** @var KeyValueObject $val */
            if($val->getValue() === $value){
                $this->removeAtIndex($index);
            }
        }
    }
}


class BasicCollection implements CollectionInterface
{
    protected $_collection = [];
    protected $_position = 0;

    public function add($element)
    {
        $this->_collection[] = $element;
    }

    public function insert($index, $element)
    {
        array_splice($this->_collection, $index, 0, $element);
    }

    public function clear()
    {
        $this->_collection = [];
    }

    public function contains($element)
    {
        foreach($this->_collection as $item){
            if($item === $element){
                return true;
            }
        }
        return false;
    }

    public function equals(CollectionInterface $collection)
    {
        if($collection->count() !== $this->count()){
            return false;
        }
        foreach($collection as $key => $item){
            if($this->_collection[$key] !== $item) {
                return false;
            }
        }
        return true;
    }

    public function get($index)
    {
        if(!array_key_exists($index, $this->_collection)){
            throw new \InvalidArgumentException("No key exists or {$index}");
        }
        return $this->_collection[$index];
    }

    public function indexOf($element)
    {
        foreach($this->_collection as $key => $item){
            if($item === $element){
                return $key;
            }
        }
        return false;
    }

    public function isEmpty()
    {
        return !($this->count() > 0);
    }

    public function remove($item)
    {
        array_splice($this->_collection, $this->indexOf($item), 1);
    }

    public function removeAtIndex($index)
    {
        array_splice($this->_collection, $index, 1);
    }

    public function count()
    {
        return count($this->_collection);
    }

    public function toArray()
    {
        return $this->_collection;
    }

    public function first()
    {
        return $this->isEmpty() ? null : $this->_collection[0];
    }

    public function last()
    {
        return $this->isEmpty() ? null : $this->_collection[$this->count()];
    }

    public function current()
    {
        return $this->_collection[$this->_position];
    }

    public function key()
    {
        return $this->_position;
    }

    public function rewind()
    {
        $this->_position = 0;
    }

    public function valid()
    {
        return isset($this->_collection[$this->_position]);
    }

    public function next()
    {
        ++$this->_position;
    }
}