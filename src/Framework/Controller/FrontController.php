<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/19/15
 * Time: 9:49 PM
 */

namespace Framework\Controller;


use Framework\View\Handler\InputOutputHandlerInterface;
use Framework\View\TemplateView;
use Framework\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FrontController implements ControllerInterface
{
    use RequestResponseTrait;

    /** @var  FrontController $instance */
    private static $instance;
    /** @var  ControllerInterface $controller */
    private $controller;
    /** @var  User $currentUser */
    private $currentUser;
    /** @var  string $rootPath */
    private static $rootPath;
    /** @var  InputOutputHandlerInterface $ioHandler */
    private $ioHandler;

    /** @var ViewInterface view */
    private $view;


    private function __construct()
    {
        $this->view = new TemplateView();
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new FrontController();
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
     * @return ViewInterface
     */
    public function getView()
    {
        return $this->getIoHandler()->getView();
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
        try {
            if (!$this->getIoHandler()) {
                throw new \Exception("You must set an input/output handler!");
            }
            $this->request = $request ?: Request::createFromGlobals();
            $this->setResponse($this->getIoHandler()->transform($this->request));
            return $this->getResponse();
        } catch (\Exception $e){
            $eolChar = php_sapi_name() == "cli" ? PHP_EOL : "</br>";
            $message = "Exit with code {$e->getCode()}: {$e->getMessage()}".$eolChar;
            $message .= "File: {$e->getFile()}, Line: {$e->getLine()}".$eolChar;
            $message .= "{$e->getCode()}".$eolChar;
            $message .= php_sapi_name() == "cli" ? $e->getTraceAsString() : nl2br($e->getTraceAsString());
            die($message);
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

    public function destroy()
    {
        static::$instance = null;
    }
}