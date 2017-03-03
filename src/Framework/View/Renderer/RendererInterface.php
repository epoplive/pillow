<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/20/15
 * Time: 9:17 PM
 */

namespace Framework\View\Renderer;


/**
 * Interface TemplateInterface
 *
 * @package Framework\View\Template
 */
interface RendererInterface
{
    /**
     * Render the view into a string and return for output
     *
     * @return \Closure
     */
    public function getRenderer();
}