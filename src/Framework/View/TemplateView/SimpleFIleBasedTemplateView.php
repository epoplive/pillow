<?php
/**
 * SimpleFileBasedTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;


class SimpleFileBasedTemplateView extends AbstractBaseTemplateView
{
    /**
     * Render the view into a string and return for output
     *
     * @param null|Array $input
     * @return string
     */
    public function render(Array $input = null)
    {
        return $this->renderScoped($this->getTemplatePath(), $input);
    }

}