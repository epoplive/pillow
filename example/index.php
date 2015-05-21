<?php
/**
 * index.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */
use Framework\Request\Filter\FilterManager;
use Symfony\Component\HttpFoundation\Request;

include_once __DIR__."/../vendor/autoload.php";


class AuthFilter implements \Framework\Request\Filter\FilterInterface {
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function execute(Request $request)
    {
        var_dump("authing bros");
    }

}

class RatelimitFilter implements \Framework\Request\Filter\FilterInterface {
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function execute(Request $request)
    {
        var_dump("ratelimiting bro");
    }

}

$fc = \Framework\Controller\FrontController::getInstance();
$filterManager = new FilterManager($fc);
$filterManager->addFilter(new AuthFilter());
$filterManager->addFilter(new RatelimitFilter());
$filterManager->filterRequest(Request::createFromGlobals());