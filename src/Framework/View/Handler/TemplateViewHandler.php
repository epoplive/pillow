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
     * @param Response $response
     * @param array $data
     * @return Response
     * @throws \Exception
     */
    public function transform(Response $response, Array $data = null){
        $route = $request->attributes->get("route");
        if(is_callable([$route, "getViewClass"]) && !empty($route->getViewClass())){
            $this->setTemplateClass($route->getViewClass());
        }

        if(!$this->getTemplate() instanceof TemplateViewInterface || $this->getTemplateClass() !== get_class($this->getTemplate())){
            $this->template = new $this->templateClass();
        }

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
     * @return TemplateViewInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param TemplateViewInterface $template
     */
    public function setTemplate(TemplateViewInterface $template)
    {
        $this->template = $template;
        $this->setTemplateClass(get_class($template));
    }
}