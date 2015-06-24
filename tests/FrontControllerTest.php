<?php
/**
 * FrontControllerTest.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace pillow\tests;


use Framework\Controller\ControllerInterface;
use Framework\Controller\FrontController;
use Framework\Request\Filter\FilterInterface;
use Framework\View\Handler\HttpInputOutputHandler;
use Framework\View\Handler\InputOutputHandlerInterface;
use Framework\View\Handler\TemplateViewHandler;
use Framework\View\Handler\ViewHandlerInterface;
use Framework\View\TemplateView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Framework\Route\Route;

class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionCode 500
     */
    public function testNoRouteFile(){
        $fc = FrontController::getInstance();
        $fc->destroy();
        unset($fc);
        HttpInputOutputHandler::$routesFile = "/Some/Non-Existant/And/Definately/Fake/File.php";
        $fc = FrontController::getInstance();
        $fc->setIoHandler(new \Framework\View\Handler\HttpInputOutputHandler(new TemplateView()));
        $fc->destroy();
    }

    public function testHasRouteFile(){
        HttpInputOutputHandler::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $this->assertNotEmpty($routes);
        $fc = FrontController::getInstance();
        $fc->setIoHandler(new \Framework\View\Handler\HttpInputOutputHandler(new TemplateView()));
        $fcRoutes = $fc->getIoHandler()->getRoutes();
        $this->assertEquals($routes, $fcRoutes);
        $fc->destroy();
    }

    public function testGetSetIoHandler()
    {
        $fc = FrontController::getInstance();
        $this->assertNull($fc->getIoHandler());
        $ioHandler = $this->getMock(InputOutputHandlerInterface::class);
        $this->assertNotNull($ioHandler);
        $fc->setIoHandler($ioHandler);
        $this->assertSame($ioHandler, $fc->getIoHandler());
        $fc->destroy();
    }

    public function testGetSetCurrentUser(){
        $fc = FrontController::getInstance();
        $this->assertNull($fc->getCurrentUser());
        $user = $this->getMock(\User::class);
        $fc->setCurrentUser($user);
        $this->assertSame($user, $fc->getCurrentUser());
        $fc->destroy();
    }

    public function testExecute(){
        HttpInputOutputHandler::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $fc->setIoHandler(new \Framework\View\Handler\HttpInputOutputHandler(new TemplateView()));
        $request = $this->getMock(Request::class);
        $response = $fc->execute($request);
        $this->assertTrue($response instanceof Response);
        $fc->destroy();
    }

    public function testAddRemoveFilter(){
        HttpInputOutputHandler::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $fc->setIoHandler(new \Framework\View\Handler\HttpInputOutputHandler(new TemplateView()));
        $filter = $this->getMock(FilterInterface::class);
        $this->assertEmpty($fc->getIoHandler()->getFilterChain()->getFilters());
        $fc->getIoHandler()->addFilter($filter);
        $this->assertEquals(1, count($fc->getIoHandler()->getFilterChain()->getFilters()));
        $fc->destroy();
    }

    public function testGetSetRoute(){
        HttpInputOutputHandler::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $fc->setIoHandler(new \Framework\View\Handler\HttpInputOutputHandler(new TemplateView()));
        $route = $this->getMock(Route::class);
        $this->assertNull($fc->getIoHandler()->getRoute());
        $fc->getIoHandler()->setRoute($route);
        $this->assertSame($route, $fc->getIoHandler()->getRoute());
        $fc->destroy();
    }

    public function testGetSetController(){
        HttpInputOutputHandler::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $controller = $this->getMock(ControllerInterface::class);
        $this->assertNull($fc->getController());
        $fc->setController($controller);
        $this->assertSame($controller, $fc->getController());
        $fc->destroy();
    }

    public function testGetSetRequest(){
        HttpInputOutputHandler::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $request = $this->getMock(Request::class);
        $fc->setRequest($request);
        $this->assertSame($request, $fc->getRequest());
        $fc->destroy();
    }

    public function testGetSetResponse(){
        HttpInputOutputHandler::$routesFile = __DIR__."/Fixtures/routes.php";
        $routes = include(__DIR__."/Fixtures/routes.php");
        $fc = FrontController::getInstance();
        $response = $this->getMock(Response::class);
        $fc->setResponse($response);
        $this->assertSame($response, $fc->getResponse());
        $fc->destroy();
    }
}
