<?php
/**
 * RouteHelper.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Route;


class RouteHTTPMethod
{
    protected $method;
    protected $action;
    protected $filters;

    /**
     * RouteHTTPMethod constructor.
     *
     * @param $method
     * @param $action
     * @param $filters
     */
    public function __construct($method, $action = null, $filters = null)
    {
        $this->method = $method;
        $this->action = $action ?: $method;
        $this->filters = $filters ?: null;
    }

    public function getFilters(){
        if(is_callable($this->filters)){
            $filters =  $this->filters;
            return $filters();
        }
        return $this->filters;
    }

    public function __call($method, $args) {
        if (property_exists($this, $method) && is_callable($this->$method)) {
            return call_user_func_array($this->$method, $args);
        }
    }
}