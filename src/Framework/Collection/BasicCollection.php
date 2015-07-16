<?php
/**
 * BasicCollection.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Collection;


class BasicCollection implements CollectionInterface
{
    protected $_collection;
    protected $_position;

    /**
     * BasicCollection constructor.
     *
     * @param array $items
     */
    public function __construct(Array $collection = null)
    {
        $this->_collection = $collection ?: [];
        $this->_position = 0;
    }


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