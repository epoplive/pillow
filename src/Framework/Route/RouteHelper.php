<?php
/**
 * RouteHelper.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Route;


class RouteHelper
{
    const HTTP_METHOD_GET       = 0b000000001;
    const HTTP_METHOD_HEAD      = 0b000000010;
    const HTTP_METHOD_PUT       = 0b000000100;
    const HTTP_METHOD_POST      = 0b000001000;
    const HTTP_METHOD_PATCH     = 0b000010000;
    const HTTP_METHOD_DELETE    = 0b000100000;
    const HTTP_METHOD_CONNECT   = 0b001000000;
    const HTTP_METHOD_OPTIONS   = 0b010000000;
    const HTTP_METHOD_TRACE     = 0b100000000;

    const HTTP_METHOD_LIST = [
        self::HTTP_METHOD_GET       => "GET",
        self::HTTP_METHOD_HEAD      => "HEAD",
        self::HTTP_METHOD_POST      => "POST",
        self::HTTP_METHOD_PUT       => "PUT",
        self::HTTP_METHOD_PATCH     => "PATCH",
        self::HTTP_METHOD_DELETE    => "DELETE",
        self::HTTP_METHOD_CONNECT   => "CONNECT",
        self::HTTP_METHOD_OPTIONS   => "OPTIONS",
        self::HTTP_METHOD_TRACE     => "TRACE",
    ];

    public static function parseMethod($method){
        if(is_string($method)){
            $method = strtoupper($method);
            if(!in_array($method, static::HTTP_METHOD_LIST)){
                throw new \Exception("Invalid HTTP Method specified!", 500);
            } else {
                return [array_flip(static::HTTP_METHOD_LIST)[$method] => $method];
            }
        } else if(is_integer($method)){
            $methods = [];
            foreach(static::HTTP_METHOD_LIST as $int => $str){
                if($method & $int){
                    $methods[$int] = $str;
                }
            }
            return $methods;
        } else {
            throw new \Exception("HTTP method must be specified as a string or integer!");
        }
    }

    public static function setCallable(Callable $func)
    {
        return $func;
    }

}