<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/8/15
 * Time: 2:55 PM
 */

namespace Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use ReflectionClass;
use JMS\Serializer\Annotation;

abstract class Entity {
    /**
     * @Annotation\Exclude
     * @var EntityManager $em
     */
    protected static $em;
    /** @Annotation\Exclude */
    protected static $db;
    /** @Annotation\Exclude */
    protected static $tableDescription = null;

    /**
     * Entity constructor.
     */
    public function __construct(Array $data = null)
    {
        if($data){
            $this->exchangeArray($data);
        }
    }

    /**
     * @return EntityManager
     */
    public static function getEm()
    {
        if(!static::$em instanceof EntityManager){
            $helper = include("src/cli_config.php");
            static::$em = $helper->get("em")->getEntityManager();
            static::$db = $helper->get("db");
        }
        return static::$em;
    }


    private function invokeSetter($key, &$value){
        $setterName = "set".str_replace(" ", "", ucwords(str_replace("_", " ", $key)));
        if(method_exists($this, $setterName)){
            $this->{$setterName}($value);
        }
    }

    public function exchangeArray($array){
        foreach($array as $key => &$value){
            $this->invokeSetter($key, $value);
        }
    }

    public function save()
    {
        $values = $this->getTableArray();
        if($this->recordExists()){  //do an update
            $sql = "UPDATE ".static::getAnnotatedDescribe()["table"]." SET ";
            array_walk($values, function(&$value, $column){
                $value = "{$column} = ".$this->getSQLValue($column);
            });
            $sql .= implode(", ", $values);
            $sql .= " WHERE {$this->generatedPKCheckSQL()}";
        } else {  // do an insert
            $sql = "INSERT INTO ".static::getAnnotatedDescribe()["table"] ." (".implode(", ", array_keys((array)$values)).") VALUES (";
            array_walk($values, function(&$value, $column){
                $value = $this->getSQLValue($column);
            });
            $sql .= implode(", ", $values).")";
        }
        $this->entitySaveHook($sql);
        $stmt = static::getEm()->getConnection()->prepare($sql);
        $stmt->execute();

        foreach(static::getAnnotatedDescribe()["relations"] as $fieldName){
            $val = $this->getViaGetter($fieldName);
            if($val instanceof Entity){
                $val->save();
            } else if($val instanceof ArrayCollection){
                foreach($val as $item){
                    $item->save();
                }
            }
        }
    }

    //override this if you need to do something with the raw save sql query (for instance add the listing id
    protected function entitySaveHook(&$sql){}

    public function remove(){
        $sql = "DELETE FROM ".static::getAnnotatedDescribe()["table"]." WHERE ".$this->generatedPKCheckSQL();
        $stmt = static::getEm()->getConnection()->prepare($sql);
        $stmt->execute();
    }

    public function recordExists(){
        $sql = "SELECT COUNT(*) FROM ".static::getAnnotatedDescribe()["table"]." WHERE ".$this->generatedPKCheckSQL();
        $stmt = static::getEm()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn(0) > 0;
    }

    protected function generatedPKCheckSQL(){
        $out = $this->getKey();
        array_walk($out, function(&$value, $key) {
            $value = "{$key}={$this->getSQLValue($key)}";
        });
        return "(".implode(" AND ", $out).")";
    }

    protected function getSQLValue($fieldName){
        switch(strtolower(static::getAnnotatedDescribe()["describe"][$fieldName]->type)){
            case 'integer':
                return (int)$this->getViaGetter($fieldName);
                break;
            case 'float':
            case 'decimal':
                return (float)$this->getViaGetter($fieldName);
                break;
            case 'boolean':
                $varVal = (boolean)$this->getViaGetter($fieldName) ? "true" : "false";
                return $varVal;
                break;
            case 'datetime':
                $val = $this->getViaGetter($fieldName);
                if(empty($val) && static::getAnnotatedDescribe()["describe"][$fieldName]->nullable == true) {
                    return 'null';
                } else if($val instanceof \DateTime){
                    return "'".pg_escape_string($val->format("Y-m-d H:i:s.uO"))."'";
                }
                break;
            case 'string':
            case 'text';
            default:
                $val = $this->getViaGetter($fieldName);
                if(!empty($val)){
                    $val = pg_escape_string($val);
                    return "'{$val}'";
                }
                if(static::getAnnotatedDescribe()["describe"][$fieldName]->nullable != true) {
                    return "''";
                }
            return 'null';
        };
    }

    public function getTableArray(){
        $out = [];
        foreach(static::getAnnotatedDescribe()["describe"] as $fieldName => $field){
            $out[$fieldName] = $this->getViaGetter($fieldName);
        }
        return $out;
    }

    public function getKey(){
        $key = [];
        foreach(static::getPrimaryKey() as $fieldName => $field) {
            $key[$field->name] = $this->getViaGetter($fieldName);
        }
        return $key;
    }

    protected function getViaGetter($fieldString){
        $getterName = "get".str_replace(" ", "", ucwords(str_replace("_", " ", $fieldString)));
        if(method_exists($this, $getterName)){
            return $this->{$getterName}();
        }
        return null;
    }

    public static function getPrimaryKey(){
        return array_intersect_key(static::getAnnotatedDescribe()["describe"], array_flip(static::getAnnotatedDescribe()["id"]));
    }

    public static function getAnnotatedDescribe(){
        if(static::$tableDescription){
            return static::$tableDescription;
        }

        $reflect = new ReflectionClass(static::class);
        $props = $reflect->getProperties();
        $return = [
            "id"        => [],
            "describe"  => [],
            "relations" => [],
        ];

        preg_match('#@(.*?)Table\(.*name="(?<name>[^"]+)".*\)\n#Us', $reflect->getDocComment(), $tableDef);
        if(isset($tableDef["name"])){
            $return["table"] = $tableDef["name"];
        }

        foreach($props as $k => $r){
            $doc = $r->getDocComment();
            $obj = (object)["id" => false, "varName" => $r->getName(), "nullable" => false];
            preg_match_all('#@(.*\\\)?Column\((?<settings>.*)\)\n#Us', $doc, $annotations);
            $isId = false;
            $isTableProp = false;
            if(isset($annotations["settings"][0])){
                $settings = explode(",", $annotations["settings"][0]);
                array_walk($settings, function($value) use ($obj) {
                    list($key, $value) = explode("=", trim($value), 2);
                    $obj->{$key} = trim($value, "\" ");
                });
                if(preg_match("#@(.*?)Id\n#s", $doc)){
                    $obj->id = true;
                    preg_match('#@(.*?)GeneratedValue\(strategy="(?<strategy>.*)"\)\n#Us', $r->getDocComment(), $generatedValue);
                    if(isset($generatedValue["strategy"])){
                        $obj->generatedValue = true;
                        $obj->generatedValueStrategy = $generatedValue["strategy"];

                        preg_match('#@(.*?)SequenceGenerator\((?<settings>.*)\)\n#Us', $r->getDocComment(), $sequenceGenerator);
                        if(isset($sequenceGenerator["settings"])){
                            $obj->sequenceGenerator = new \stdClass();
                            $settings = explode(",", $sequenceGenerator["settings"]);
                            array_walk($settings, function($value) use ($obj) {
                                list($key, $value) = explode("=", trim($value), 2);
                                $obj->sequenceGenerator->{$key} = trim($value, "\" ");
                            });
                        }
                    }
                    $isId = true;
                }
                $isTableProp = true;
            }

            preg_match_all('#@(.*?\\\)?JoinColumn\(.*name="(?<name>[^"]+)".*\)\n#Us', $doc, $joins);
            if(isset($joins["name"][0])){
                $obj->name = $joins["name"][0];
                if(preg_match("#@(.*?)Id\n#s", $doc)){
                    $obj->id = true;
                    preg_match('#@(.*?)GeneratedValue\(strategy="(?<strategy>.*)"\)\n#Us', $r->getDocComment(), $generatedValue);
                    if(isset($generatedValue["strategy"])){
                        $obj->generatedValue = true;
                        $obj->generatedValueStrategy = $generatedValue["strategy"];

                        preg_match('#@(.*?)SequenceGenerator\((?<settings>.*)\)\n#Us', $r->getDocComment(), $sequenceGenerator);
                        if(isset($sequenceGenerator["settings"])){
                            $obj->sequenceGenerator = new \stdClass();
                            $settings = explode(",", $sequenceGenerator["settings"]);
                            array_walk($settings, function($value) use ($obj) {
                                list($key, $value) = explode("=", trim($value), 2);
                                $obj->sequenceGenerator->{$key} = trim($value, "\" ");
                            });
                        }
                    }
                    if(!property_exists($obj, "type")){
                        $obj->type = "integer";
                    }
                    $isId = true;
                }
                $isTableProp = true;
            }


            preg_match('#@(.*?)?(?<joinType>OneToOne|OneToMany|ManyToOne|ManyToMany)\((.*?)cascade=\{"(?<cascade>[^"]+)".*\)\n#s', $doc, $matches);
            if(isset($matches["joinType"])){
                if(isset($matches["cascade"][0]) && strtolower($matches["cascade"]) === "persist"){
                    $return["relations"][] = $r->getName();
                }
            }

            if($isId){
                $return["id"][] = $obj->name;
            }
            if($isTableProp){
                $return["describe"][$obj->name] = $obj;
            }
        }
        static::$tableDescription = $return;
        return static::$tableDescription;
    }
}