<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/20/15
 * Time: 9:17 PM
 */

namespace Framework\View\TemplateView;


/**
 * Interface TemplateViewInterface
 *
 * @package Framework\View\TemplateView
 */
interface TemplateViewInterface
{
    /**
     * return the view input
     * @return Array
     */
    public function getInput();

    /**
     * @param Array $input
     */
    public function setInput($input = null);

    /**
     * @param string $template
     */
    public function setTemplate($template);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * Render the view into a string and return for output
     *
     * @param null|Array $input
     * @return string
     */
    public function render(Array $input = null);
}