<?php
/**
 * KeyValueCollection.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Collection;

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