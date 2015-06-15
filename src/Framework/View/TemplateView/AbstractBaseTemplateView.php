<?php
/**
 * AbstractBaseTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;

use Framework\Controller\FrontController;


/**
 * Class AbstractBaseTemplateView
 *
 * @package Framework\View\TemplateView
 */
abstract class AbstractBaseTemplateView implements TemplateViewInterface
{
    /** @var  Array $input */
    protected $input;
    /** @var  string $template */
    protected $template;
    /** @var  Array $config */
    protected $config;

    /**
     * AbstractBaseTemplateView constructor.
     */
    public function __construct(Array $config = null)
    {
        $this->config = $config ?: null;
        if (is_array($config) && count($config) > 0) {
            foreach ($this->config as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * return the view input
     *
     * @return Array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param Array $input
     */
    public function setInput($input = null)
    {
        $this->input = $input;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Render the view into a string and return for output
     *
     * @param mixed $input
     * @return string
     */
    abstract public function render($input = null);
}