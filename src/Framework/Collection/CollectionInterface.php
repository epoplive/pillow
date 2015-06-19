<?php
/**
 * CollectionInterface.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Collection;

interface CollectionInterface extends \Iterator
{
    public function add($element);
    public function insert($index, $element);
    public function clear();
    public function contains($element);
    public function equals(CollectionInterface $collection);
    public function get($index);
    public function indexOf($element);
    public function isEmpty();
    public function remove($element);
    public function removeAtIndex($index);
    public function count();
    public function toArray();
    public function first();
    public function last();
    public function current();
    public function key();
    public function rewind();
    public function valid();
    public function next();
}