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
use Framework\View\Renderer\FileRenderer;

class TemplateView implements ViewInterface
{
    /** @var  KeyValueCollection $templateRegistry */
    protected $templateRegistry;
    /** @var array $data */
    protected $data;
    /** @var  string $template */
    protected $template;  // this is the currently used template name registered in the registry, used internally

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
     * @param mixed $template
     * @param $alias
     * @return TemplateInterface
     * @throws \Exception
     */
    public function registerTemplate($template, $alias)
    {
        if($this->getTemplateRegistry()->findIndexByKey($alias)) {
            throw new \Exception("View template with this alias already registered!");
        }
        if(!$template instanceof \Closure) {
            $renderer = new FileRenderer();
            $template = $renderer->getRenderer();
        }
        $this->getTemplateRegistry()->add(new KeyValueObject($alias, $template));
        return $template;
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
        if($template instanceof \Closure) {
            $output = $template;
        } else if(!$output = $this->getTemplateRegistry()->findIndexByKey($template)) {
            $renderer = new FileRenderer();
            $output = $renderer->getRenderer();
        }
        $output = \Closure::bind($output, $this);
        return $output->__invoke($template, $data);
    }

    /**
     * @param \Exception $e
     * @return string
     */
    public function handleException(\Exception $e){
        $eolChar = php_sapi_name() == "cli" ? PHP_EOL : "</br>";
        $message = "Exit with code {$e->getCode()}: {$e->getMessage()}".$eolChar;
        $message .= "File: {$e->getFile()}, Line: {$e->getLine()}".$eolChar;
        $message .= "{$e->getCode()}".$eolChar;
        $message .= php_sapi_name() == "cli" ? $e->getTraceAsString() : nl2br($e->getTraceAsString());

        return $message;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(Array $data = null)
    {
        $this->data = $data;
    }

    public function updateData(Array $data = null)
    {
        foreach($data as $key => $value){
            $this->data[$key] = $value;
        }
    }

}