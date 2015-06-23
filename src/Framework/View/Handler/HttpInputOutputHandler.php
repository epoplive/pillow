<?php
/**
 * HttpInputOutputHandler.php.
 * Author: brett.thomas@gmail.com
 * Date: 6/21/15
 * Time: 6:50 PM
 */

namespace Framework\View\Handler;


use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Framework\Request\Filter\FilterChain;
use Framework\Request\Filter\FilterInterface;
use Framework\Request\Filter\FilterManagerInterface;
use Framework\Request\Filter\FilterManagerTrait;
use Framework\Route\Route;
use Framework\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpInputOutputHandler implements InputOutputHandlerInterface, FilterManagerInterface
{
    use FilterManagerTrait;

    /** @var  Request $request */
    protected $input;
    /** @var  Response $response */
    protected $output;
    /** @var  Dispatcher $dispatcher */
    private $dispatcher;
    /** @var  Route $route */
    private $route;

    private $routes;

    /** @var  ViewInterface $view */
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

    public function __construct(ViewInterface $view)
    {
        $this->setView($view);
        if (stream_resolve_include_path(static::$routesFile) === false) {
            throw new \Exception("Unable to load routes file from include path.");
        }
        $routes = include static::$routesFile;
        if (empty($routes)) {
            throw new \Exception("No routes found!", 500);
        }
        $this->routes = $routes;
        $this->filterChain = new FilterChain();

        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routes) {
            $addRoute = function (RouteCollector &$r, Array $route) {
                foreach ($route["methods"] as $key => $value) {
                    if (in_array(strtoupper($key), static::$httpMethods)) {
                        $method = strtoupper($key);
                    } else if (is_numeric($key)) {
                        $method = strtoupper($value);
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

    }

    public function transform($input)
    {
        return $this->_transform($input);
    }

    /**
     * @return ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param ViewInterface $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Internal version of the transform function that allows benefit from error handing of type hints
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    protected function _transform(Request $request)
    {
        $response = new Response();
        try {
            $this->route = $this->route($request);
            $this->filterRequest($request);
            $request->attributes->add(["route", $this->route]);
            $params = [];

            if (!($controllerName = $this->route->getController()) || !class_exists($this->route->getController())) {
                throw new \Exception("Invalid controller class!");
            }

            $controller = new $controllerName($request);
            if (!method_exists($controller, $this->route->getAction())) {
                throw new \Exception("Invalid controller method: {$this->route->getAction()}");
            }

            $reflection = new \ReflectionClass($controller);
            foreach ($reflection->getMethod($this->route->getAction())->getParameters() as $key => $param) {
                if (property_exists($param, "name") && array_key_exists($param->name, (array)$this->route->getVars())) {
                    $params[$key] = $this->route->getVars()[$param->name];
                }
            }
            $controllerReturn = $controller->{$this->route->getAction()}(...$params);
            if ($controllerReturn instanceof Response) {
                $response = $controllerReturn;
            } else {
                $response->setContent($this->getView()->render($this->route->getView(), $this->getView()->getData()));
                $response->setStatusCode(200);
            }
        } catch(\Exception $e){
            $response->setContent($this->getView()->handleException($e));
            try {
                $response->setStatusCode($e->getCode());
            } catch (\InvalidArgumentException $e) {
                $response->setStatusCode(500);
            }
        }

        try {
            $this->filterResponse($response);
        } catch (\InvalidArgumentException $e) {
            $response->setStatusCode(500);
        }
        return $response;
    }

    private function route(Request $request)
    {
        $routeInfo = $this->dispatcher->dispatch(
            $request->getMethod(),
            parse_url($request->getRequestUri(), PHP_URL_PATH)
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

                // clean up the method names in the route array so we don't have to worry about case
                if (array_key_exists("methods", $route)) {
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
                if (array_key_exists(strtoupper($request->getMethod()), $route["methods"])) {
                    if (is_array($route["methods"][strtoupper($request->getMethod())])) {
                        if (!isset($route["methods"][strtoupper($request->getMethod())]["action"])) {
//                            throw new \Exception("Action not specified!");
                            $route["action"] = $request->getMethod() . "Action";
                        } else {
                            $route["action"] = $route["methods"][strtoupper($request->getMethod())]["action"];
                        }

                        if (isset($route["methods"][strtoupper($request->getMethod())]["requestFilters"])) {
                            $reqFilter = $route["methods"][strtoupper($request->getMethod())]["requestFilters"];
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
                        $route["action"] = $route["methods"][strtoupper($request->getMethod())];
                    }
                } else {
                    if (in_array(strtoupper($request->getMethod()), $route["methods"])) {
                        $route["action"] = strtolower($request->getMethod()) . "Action";
                    }
                }

                return new Route(
                    $route["uri"],
                    $request->getMethod(),
                    $route["controller"],
                    $route["action"],
                    isset($route["viewClass"]) ? $route["viewClass"] : null,
                    isset($route["view"]) ? $route["view"] : null,
                    $vars
                );

                break;
            default:
                error_log(__METHOD__ . ":An unknown error has occurred!");
                throw new \Exception("An unknown error has occurred!", 400);
        }
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
}