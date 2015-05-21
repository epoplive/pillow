<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/19/15
 * Time: 9:49 PM
 */

namespace Framework\Controller;


use FastRoute\RouteCollector;
use Framework\Request\Filter\FilterManagerTrait;
use Framework\Route\Route;
use Framework\View\TemplateView\SimpleJSONTemplateView;
use Symfony\Component\HttpFoundation\Request;

class FrontController implements ControllerInterface
{
    use RequestResponseTrait;
    use FilterManagerTrait;

    /** @var  FrontController $instance */
    private static $instance;
    private $dispatcher;
    /** @var  Route $route */
    private $route;
    /** @var  ControllerInterface $controller */
    private $controller;

    private static $httpMethods = [
        "GET",
        "PUT",
        "POST",
        "PATCH",
        "DELETE",
        "HEAD",
        "TRACE",
        "CONNECT",
        "OPTIONS",
    ];

    private $routes;

    private function __construct(Array $routes)
    {
        $this->routes = $routes;
    }

    private function __clone()
    {

    }

    public function execute(Request $request){
        if (!$request) {
            $request = Request::createFromGlobals();
        }
        $this->request = $request;
        $routes = $this->routes;
        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                foreach ($route["methods"] as $key => $value) {
                    if(in_array(strtoupper($key), static::$httpMethods)){
                        $method = strtoupper($key);
                    } else if(is_numeric($key)){
                        $method = strtoupper($value);
                    }
                    $r->addRoute($method, $route["uri"], $route);
                }
            }
        });
        $routeInfo = $this->dispatcher->dispatch($this->getRequest()->getMethod(), $this->getRequest()->getRequestUri());
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                throw new \Exception("Not Found.", 404);
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                throw new \Exception("Method not allowed.", 405);
                break;
            case \FastRoute\Dispatcher::FOUND:

                $route = $routeInfo[1];
                $vars = $routeInfo[2];
                if (!isset($route["controller"]) && !class_exists($route["controller"])) {
                    throw new \Exception("Invalid controller class!");
                }

                $this->controller = new $route["controller"]($this->request);
                if (!isset($route["action"])) {
                    $route["action"] = strtolower($this->request->getMethod());
                }
                if (!method_exists($this->controller, $route["action"])) {
                    throw new \Exception("Invalid controller method: {$route["action"]}");
                }
                $this->route = new Route($route["uri"], $this->request->getMethod(), $route["controller"], $route["action"]);
                break;
            default:
                throw new \Exception("An unknown error has occurred!");
        }

        $data = $this->controller->{$this->route->getAction()}();
        $templateClass = $this->route->getViewClass() ?: SimpleJSONTemplateView::class;
        $template = new $templateClass();
        $template->render($data);
    }


    public static function getInstance()
    {
        if (!static::$instance) {
            $routes = include "config/routes.php";
            static::$instance = new FrontController($routes);
        }
        return static::$instance;
    }

    /**
     * @return RouteCollector
     */
    public function getRouteCollector()
    {
        return $this->routeCollector;
    }

    /**
     * @param RouteCollector $routeCollector
     */
    public function setRouteCollector($routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }


}