<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/25/15
 * Time: 3:16 PM
 */
namespace Entity;


/**
 * @ExclusionPolicy("all")
 */
interface EntityInterface
{
    public function exchangeArray($array);

    public function save($cascade = true);

    public function remove();

    public function recordExists();

    public function getTableArray($clearEmptyPrimaryKeys = false);

    public function getKey();

    public static function getPrimaryKey();

    public static function getAnnotatedDescribe();
}