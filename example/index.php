<?php
/**
 * index.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */
use Framework\Request\Filter\FilterManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

include_once __DIR__."/../vendor/autoload.php";


class AuthFilter implements \Framework\Request\Filter\FilterInterface
{
    /**
     * @param Request $request
     * @return bool
     */
    public function filterRequest(Request $request)
    {
        var_dump("authing bros");
    }

    /**
     * @param Response $response
     * @return bool
     */
    public function filterResponse(Response $response)
    {
        // TODO: Implement filterResponse() method.
    }

}

class RatelimitFilter implements \Framework\Request\Filter\FilterInterface {
    /**
     * @param Request $request
     * @return bool
     */
    public function filterRequest(Request $request)
    {
        var_dump("ratelimiting bro");
    }

    /**
     * @param Response $response
     * @return bool
     */
    public function filterResponse(Response $response)
    {
        // TODO: Implement filterResponse() method.
    }

}

$fc = \Framework\Controller\FrontController::getInstance();
$fc->addFilter(new AuthFilter());
$fc->addFilter(new RatelimitFilter());
$response = $fc->execute(Request::createFromGlobals());
$response->send();
