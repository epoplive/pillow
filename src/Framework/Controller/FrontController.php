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
use Framework\View\TemplateView\SimpleFileBasedTemplateView;
use Framework\View\TemplateView\SimpleJSONTemplateView;
use Framework\View\TemplateView\TemplateViewInterface;
use Symfony\Component\HttpFoundation\Request;

final class FrontController implements ControllerInterface
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

    private static $rootPath;

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

    public static function getRootPath(){
        if(defined("PILLOW_ROOT_PATH")){
            self::$rootPath = PILLOW_ROOT_PATH;
        } else if(!isset(self::$rootPath)){
            $path = explode(DIRECTORY_SEPARATOR, __DIR__);
            while(array_pop($path) !== 'src');
            self::$rootPath = implode(DIRECTORY_SEPARATOR, $path);
        }
        return self::$rootPath;
    }

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
                if(!isset($route["templateFile"])){
                    $route["templateFile"] = null;
                }
                if(!isset($route["viewClass"])){
                    $route["viewClass"] = SimpleFileBasedTemplateView::class;
                }
                $this->route = new Route($route["uri"], $this->request->getMethod(), $route["controller"], $route["action"], $route["viewClass"], $route["templateFile"]);
                break;
            default:
                throw new \Exception("An unknown error has occurred!");
        }

        $data = $this->controller->{$this->route->getAction()}();
        $templateClass = $this->route->getViewClass();
        $template = new $templateClass($this->route->toArray());
        if(!$template instanceof TemplateViewInterface){
            throw new \Exception("Invalid template class.  Class must be an instance of ".TemplateViewInterface::class);
        }

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