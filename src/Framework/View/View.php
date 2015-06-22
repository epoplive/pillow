<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 6/20/15
 * Time: 2:11 PM
 */

namespace Framework\View;

use Framework\Collection\KeyValueCollection;
use Framework\Collection\KeyValueObject;
use Framework\View\Template\FileBasedTemplate;
use Framework\View\Template\TemplateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class View implements ViewInterface
{
    /** @var  KeyValueCollection $templateRegistry */
    protected $templateRegistry;

    /** @var array $data */
    protected $data;

    /** @var  string $template */
    protected $template;  // this is the currently used template name registered in the registry, used internally

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->templateRegistry = new KeyValueCollection();
        $this->data = [];
    }

    /**
     * @return KeyValueCollection
     */
    public function getTemplateRegistry()
    {
        return $this->templateRegistry;
    }

    /**
     * @param KeyValueCollection $templateRegistry
     */
    public function setTemplateRegistry($templateRegistry)
    {
        $this->templateRegistry = $templateRegistry;
    }

    /**
     * Registers a new template by alias in the registry for re-use
     * passing an alias will override the template's current alias
     *
     * @param TemplateInterface $template
     * @param $alias
     * @return TemplateInterface
     */
    public function registerTemplate(TemplateInterface $template, $alias = null)
    {
        if($alias) {
            $template->setAlias($alias);
        }
        $this->getTemplateRegistry()->add(new KeyValueObject($template->getAlias(), $template));
        return $template;
    }

    /**
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->getTemplateRegistry()->getByKey($this->template);
    }

    public function setTemplate($input)
    {
        if($input instanceof TemplateInterface){
            if(!$this->getTemplateRegistry()->getByValue($input)){
                $template = $this->registerTemplate($input);
            }
        } else if(!$template = $this->getTemplateRegistry()->getByKey($input)){
            $template = $this->registerTemplate(new FileBasedTemplate($input));
        }
        $this->template = $template->getAlias();
    }

    /**
     * Render a template into a string with optional input data
     *
     * $template will first check if the passed input is an instance of Template\TemplateInterface in which case it
     * only needs to render, otherwise it will look for an alias in the template registry with the name and try to
     * render that.  If that fails it will attempt to load a file with that name into a new instance of
     * Template\FileBasedTemplate.
     *
     * @param $template
     * @param array|null $data
     * @return string
     */
    public function render($template, Array $data = null)
    {
        $data = $data?: [];
        if($template instanceof TemplateInterface) {
            $output = $template;
        } else if(!$output = $this->getTemplate($template)){
            $output = new FileBasedTemplate($template);
        }
        return $output->render($data);
    }

    public function handleException(\Exception $e){
        if(!$this->getTemplate() || !is_callable([$this->template, "handleException"])){
            throw $e;
        }
        $this->template->handleException($e);
    }
}