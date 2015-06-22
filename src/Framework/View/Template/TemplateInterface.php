<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/20/15
 * Time: 9:17 PM
 */

namespace Framework\View\Template;


/**
 * Interface TemplateInterface
 *
 * @package Framework\View\Template
 */
interface TemplateInterface
{
    public function __construct($content = null, $alias = null);

    /**
     * @param string $content
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();

    /**
     * Render the view into a string and return for output
     *
     * @param mixed $input
     * @return string
     */
    public function render($input = null);

    /**
     * Return the alias of the template
     * @return string
     */
    public function getAlias();

    /**
     * @param string $alias
     */
    public function setAlias($alias);
}