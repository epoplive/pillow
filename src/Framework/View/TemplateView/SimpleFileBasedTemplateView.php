<?php
/**
 * SimpleFileBasedTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;


class SimpleFileBasedTemplateView extends AbstractBaseFileTemplateView
{
    /**
     * Render the view into a string and return for output
     *
     * @param mixed $input
     * @return string
     */
    public function render($input = null)
    {
        return $this->renderScoped($this->getTemplatePath(), (array)$input);
    }

    public function handleException(\Exception $e){
        return $this->renderScoped($this->getTemplatePath(), ["errors" => [$e]]);
    }

}