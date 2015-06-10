<?php
/**
 * TemplateViewHandler.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\Handler;


use Framework\View\TemplateView\SimpleTextTemplateView;
use Framework\View\TemplateView\TemplateViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateViewHandler implements ViewHandlerInterface
{
    protected $templateClass = SimpleTextTemplateView::class;
    protected $template;

    /**
     * TemplateViewHandler constructor.
     *
     * @param $templateClass
     */
    public function __construct($templateClass = null)
    {
        if($templateClass){
            $this->templateClass = $templateClass;
        }
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function transform(Request $request, Response $response){
        $route = $request->attributes->get("route");
        if(is_callable([$route, "getViewClass"]) && !empty($route->getViewClass())){
            $this->setTemplateClass($route->getViewClass());
        }

        $this->template = new $this->templateClass($route ? $route->toArray() : []);
        if(!$this->template instanceof TemplateViewInterface){
            throw new \Exception("Invalid template class.  Class must be an instance of ".TemplateViewInterface::class, 400);
        }
        $content = $this->template->render($request->attributes->get("viewData"));
        $response->setContent($content);
        return $response;
    }

    public function handleException(\Exception $e){
        if(!$this->template){
            $this->template = new $this->templateClass();
        }
        if(is_callable([$this->template, "handleException"])){
            $this->template->handleException($e);
        } else {
            throw $e;
        }
    }

    /**
     * @return mixed
     */
    public function getTemplateClass()
    {
        return $this->templateClass;
    }

    /**
     * @param mixed $templateClass
     */
    public function setTemplateClass($templateClass)
    {
        $this->templateClass = $templateClass;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}