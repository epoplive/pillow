<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 6/21/15
 * Time: 1:04 PM
 */

namespace Framework\View\Template;


class AbstractTemplate implements TemplateInterface
{
    protected $alias;
    protected $content;

    public function __construct($content = null, $alias = null)
    {
        $this->setContent($content);
        $this->setAlias($alias);
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Render the view into a string and return for output
     *
     * @param mixed $input
     * @return string
     */
    public function render($input = null)
    {
        $scope = function($input, $template) {
            ob_start();
            if(is_array($input)){
                extract( $input );
            }
            echo $template;
            $contents = ob_get_contents(); // get contents of buffer
            ob_end_clean();
            return $contents;
        };
        return $scope($this->getContent(), (array)$input);
    }

    /**
     * Return the alias of the template
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Sets the alias of the template for use primarily by the ViewHandler
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
}