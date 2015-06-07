<?php
/**
 * FrontControllerTest.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace pillow\tests;


use Framework\Controller\ControllerInterface;
use Framework\Controller\FrontController;
use Framework\Request\Filter\FilterInterface;
use Framework\View\Handler\TemplateViewHandler;
use Framework\View\Handler\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Framework\Route\Route;

class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testNoRouteFile(){
        FrontController::$routesFile = "/Some/Non-Existant/And/Definately/Fake/File.php";
        $fc = FrontController::getInstance();
    }

    public function testHasRouteFile(){
        FrontController::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $this->assertNotEmpty($routes);
        $fc = FrontController::getInstance();
        $this->assertEquals($routes, $fc->getRoutes());
    }

    public function testGetSetViewHandler()
    {
        $fc = FrontController::getInstance();
        $this->assertNull($fc->getViewHandler());
        $viewHandler = $this->getMock(ViewHandlerInterface::class);
        $this->assertNotNull($viewHandler);
        $fc->setViewHandler($viewHandler);
        $this->assertSame($viewHandler, $fc->getViewHandler());
    }

    public function testGetSetCurrentUser(){
        $fc = FrontController::getInstance();
        $this->assertNull($fc->getCurrentUser());
        $user = $this->getMock(\User::class);
        $fc->setCurrentUser($user);
        $this->assertSame($user, $fc->getCurrentUser());
    }

    public function testExecute(){
        FrontController::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $request = $this->getMock(Request::class);
        $response = $fc->execute($request);
        $this->assertTrue($response instanceof Response);
    }

    public function testAddRemoveFilter(){
        FrontController::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $filter = $this->getMock(FilterInterface::class);
        $this->assertEmpty($fc->getFilterChain()->getFilters());
        $fc->addFilter($filter);
        $this->assertEquals(1, count($fc->getFilterChain()->getFilters()));
    }

    public function testGetSetRoute(){
        FrontController::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $route = $this->getMock(Route::class);
        $this->assertNull($fc->getRoute());
        $fc->setRoute($route);
        $this->assertSame($route, $fc->getRoute());
    }

    public function testGetSetController(){
        FrontController::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $controller = $this->getMock(ControllerInterface::class);
        $this->assertNull($fc->getController());
        $fc->setController($controller);
        $this->assertSame($controller, $fc->getController());
    }

    public function testGetSetRequest(){
        FrontController::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $request = $this->getMock(Request::class);
        $fc->setRequest($request);
        $this->assertSame($request, $fc->getRequest());
    }

    public function testGetSetResponse(){
        FrontController::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $response = $this->getMock(Response::class);
        $fc->setResponse($response);
        $this->assertSame($response, $fc->getResponse());
    }
}
