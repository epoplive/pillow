<?php
/**
 * RouteGroup.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Route;


class RouteGroup
{

    protected $uri;
    protected $methods;
    protected $controller;
    protected $viewClass;

    public function __construct($uri, $method, $controller, $viewClass = null)
    {
        $this->uri = $uri;
        $this->viewClass = $viewClass ?: null;
        $this->methods = RouteHelper::parseMethod($method);
    }

    public static function MethodFactory($method){
        $methods = [];
        foreach(RouteHelper::parseMethod($method) as $int => $str){
//            $methods = new RouteHTTPMethod($method)
        }
    }


}