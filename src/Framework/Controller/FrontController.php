<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/19/15
 * Time: 9:49 PM
 */

namespace Framework\Controller;


use FastRoute\RouteCollector;
use Framework\Request\Filter\FilterChain;
use Framework\Request\Filter\FilterInterface;
use Framework\Request\Filter\FilterManagerTrait;
use Framework\Route\Route;
use Framework\View\Handler\InputOutputHandlerInterface;
use Framework\View\Handler\ViewHandlerInterface;
use Framework\View\View;
use Framework\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    /** @var  User $currentUser */
    private $currentUser;
    /** @var  string $rootPath */
    private static $rootPath;

    // placeholder for an object to replace the ViewHandler
    // this should take some type of input request and output some out of output request
    // the front controller then will defer responsibility to this for handling the actual logic
    // so for instance we need an httpio class that will take a http request and output a http response
    // this will also handle all of the logic for trapping error
    // having the $this->view variable still makes sense in this context because there's always a view of the output
    private $ioHandler;

    private $viewHandler;

    /** @var ViewInterface view */
    private $view;

    public static $routesFile = "config/routes.php";

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
        $this->setRoutes($routes);
        $this->filterChain = new FilterChain();
        $this->view = new View();
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!static::$instance) {
            if (stream_resolve_include_path(static::$routesFile) === false) {
                throw new \Exception("Unable to load routes file from include path.");
            }
            $routes = include static::$routesFile;
            if (empty($routes)) {
                throw new \Exception("No routes found!", 500);
            }
            static::$instance = new FrontController($routes);
        }
        return static::$instance;
    }

    public static function getRootPath()
    {
        if (defined("PILLOW_ROOT_PATH")) {
            self::$rootPath = PILLOW_ROOT_PATH;
        } else {
            if (!isset(self::$rootPath)) {
                $path = explode(DIRECTORY_SEPARATOR, __DIR__);
                while (array_pop($path) !== 'src') {
                    ;
                }
                self::$rootPath = implode(DIRECTORY_SEPARATOR, $path);
            }
        }
        return self::$rootPath;
    }

    /**
     * @return InputOutputHandlerInterface
     */
    public function getIoHandler()
    {
        return $this->ioHandler;
    }

    /**
     * @param InputOutputHandlerInterface $ioHandler
     */
    public function setIoHandler(InputOutputHandlerInterface $ioHandler)
    {
        $this->ioHandler = $ioHandler;
    }

    /**
     * @return ViewHandlerInterface
     */
    public function getViewHandler()
    {
        return $this->viewHandler;
    }

    /**
     * @param ViewHandlerInterface $viewHandler
     */
    public function setViewHandler(ViewHandlerInterface $viewHandler = null)
    {
        $this->viewHandler = $viewHandler;
    }

    /**
     * @return ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return \User
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * @param \User $currentUser
     */
    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function redirect($path)
    {
        if (php_sapi_name() !== "cli") {
            header("Location: " . $path);
            exit();
        }
        return true;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function execute(Request $request)
    {
        $this->request = $request ?: Request::createFromGlobals();
        $this->response = new Response();
        try {
            if (!$this->getIoHandler()) {
                throw new \Exception("Controller must return either a response object or you must set a view handler!");
            }

            $this->route();
            $this->filterRequest($this->request);
            $reflection = new \ReflectionClass($this->controller);
            $params = [];
            $this->request->attributes->add(["route", $this->route]);

            foreach ($reflection->getMethod($this->route->getAction())->getParameters() as $key => $param) {
                if (property_exists($param, "name") && array_key_exists($param->name, (array)$this->route->getVars())) {
                    $params[$key] = $this->route->getVars()[$param->name];
                }
            }
            $controllerReturn = $this->controller->{$this->route->getAction()}(...$params);
            if ($controllerReturn instanceof Response) {
                $this->setResponse($controllerReturn);
            } else {
                $this->request->attributes->set(
                    "viewData",
                    array_merge($this->request->attributes->get("viewData", []), (array)$controllerReturn)
                );
                $this->setResponse($this->getIoHandler()->transform($this->request, $this->response));
            }
            $this->response->setStatusCode(200);
        } catch (\Exception $e) {
            try {
                try{
                    $this->getIoHandler()->handleException($e);
                } catch (\Exception $e){
                    $eolChar = php_sapi_name() == "cli" ? PHP_EOL : "</br>";
                    $message = "Exit with code {$e->getCode()}: {$e->getMessage()}".$eolChar;
                    $message .= "File: {$e->getFile()}, Line: {$e->getLine()}".$eolChar;
                    $message .= "{$e->getCode()}".$eolChar;
                    $message .= php_sapi_name() == "cli" ? $e->getTraceAsString() : nl2br($e->getTraceAsString());

                    $this->getResponse()->setContent($message);
                    $this->getResponse()->setStatusCode($e->getCode());
                }
            } catch (\InvalidArgumentException $e) { // convert codes that are not valid http response codes
                $this->getResponse()->setStatusCode(400);
            }
        } finally {
            try {
                $this->filterResponse($this->response);
            } catch (\InvalidArgumentException $e) {
                $this->getResponse()->setContent($e->getMessage());
                $this->getResponse()->setStatusCode(500);
            }
            return $this->getResponse();
        }
    }

    private function route()
    {
        $routes = $this->routes;
        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routes) {
            $addRoute = function (RouteCollector &$r, Array $route) {
                foreach ($route["methods"] as $key => $value) {
                    if (in_array(strtoupper($key), static::$httpMethods)) {
                        $method = strtoupper($key);
                    } else {
                        if (is_numeric($key)) {
                            $method = strtoupper($value);
                        }
                    }
                    $r->addRoute($method, $route["uri"], $route);
                }
            };

            foreach ($routes as $route) {
                if (is_array($route["uri"])) {
                    foreach ($route["uri"] as $uri) {
                        $multiRoute = $route;
                        $multiRoute["uri"] = $uri;
                        $addRoute($r, $multiRoute);
                    }
                } else {
                    $addRoute($r, $route);
                }
            }
        });

        $routeInfo = $this->dispatcher->dispatch(
            $this->getRequest()->getMethod(),
            parse_url($this->getRequest()->getRequestUri(), PHP_URL_PATH)
        );
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
                if (array_key_exists("methods",
                    $route)) { // clean up the method names in the route array so we don't have to worry about case
                    $newMethods = [];
                    foreach ($route["methods"] as $key => $value) {
                        if (in_array(strtoupper($key), self::$httpMethods)) {
                            $newMethods[strtoupper($key)] = $value;
                        } else {
                            if (in_array(strtoupper($value), self::$httpMethods)) {
                                $newMethods[] = $value;
                            }
                        }
                    }
                    $route["methods"] = $newMethods;
                }
                if (array_key_exists(strtoupper($this->request->getMethod()), $route["methods"])) {
                    if (is_array($route["methods"][strtoupper($this->request->getMethod())])) {
                        if (!isset($route["methods"][strtoupper($this->request->getMethod())]["action"])) {
//                            throw new \Exception("Action not specified!");
                            $route["action"] = $this->request->getMethod() . "Action";
                        } else {
                            $route["action"] = $route["methods"][strtoupper($this->request->getMethod())]["action"];
                        }

                        if (isset($route["methods"][strtoupper($this->request->getMethod())]["requestFilters"])) {
                            $reqFilter = $route["methods"][strtoupper($this->request->getMethod())]["requestFilters"];
                            if (is_callable($reqFilter)) {
                                $out = $reqFilter($this);
                                if (is_array($out)) {
                                    foreach ($out as $filterName) {
                                        if ($filterName instanceof FilterInterface) {
                                            $this->addFilter($filterName);
                                        } else {
                                            if (is_a($filterName, FilterInterface::class, true)) {
                                                $this->addFilter(new $filterName());
                                            }
                                        }
                                    }
                                }
                            } else {
                                if (is_array($reqFilter) || is_object($reqFilter)) {
                                    foreach ($reqFilter as $filter) {
                                        $this->addFilter(new $filter());
                                    }
                                } else {
                                    $this->addFilter(new $reqFilter());
                                }
                            }
                        }
                    } else {
                        $route["action"] = $route["methods"][strtoupper($this->request->getMethod())];
                    }
                } else {
                    if (in_array(strtoupper($this->request->getMethod()), $route["methods"])) {
                        $route["action"] = strtolower($this->request->getMethod()) . "Action";
                    }
                }

                if (!method_exists($this->controller, $route["action"])) {
                    throw new \Exception("Invalid controller method: {$route["action"]}");
                }
                if (!isset($route["templateFile"])) {
                    $route["templateFile"] = null;
                }
                if (!isset($route["viewClass"])) {
                    $route["viewClass"] = null;
                }
                $this->route = new Route($route["uri"], $this->request->getMethod(), $route["controller"],
                    $route["action"], $route["viewClass"], $route["templateFile"], $vars);
                $this->request->attributes->add(["route" => $this->route]);

                break;
            default:
                error_log(__METHOD__ . ":An unknown error has occurred!");
                throw new \Exception("An unknown error has occurred!", 400);
        }
    }

    /**
     * @return ControllerInterface
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param ControllerInterface $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param Route $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param array $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    public function destroy()
    {
        static::$instance = null;
    }
}