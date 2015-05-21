<?php
/**
 * AbstractBaseTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;


/**
 * Class AbstractBaseTemplateView
 *
 * @package Framework\View\TemplateView
 */
abstract class AbstractBaseTemplateView implements TemplateViewinterface
{
    /**
     * @var  Array $input
     */
    protected $input;
    /**
     * @var  string $template
     */
    protected $template;

    /**
     * return the view input
     * @return Array
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @param Array $input
     */
    public function setInput($input = null){
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
     * @param null|Array $input
     * @return string
     */
    abstract public function render(Array $input = null);

    protected function renderScoped($file, Array $data = null){
        $scope = function() {
            // It's very simple :)
            extract( func_get_arg(1) );
            return require func_get_arg(0);
        };
        return $scope($file, (array)$data);
    }
}