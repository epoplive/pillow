<?php
/**
 * JSONTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;


use Zend\Json\Json;

class SimpleTextTemplateView extends AbstractBaseTemplateView implements TemplateViewInterface
{
    const STATUS_SUCCESS = "success";
    const STATUS_FAIL = "fail";
    const STATUS_ERROR = "error";

    /**
     * Render the view into a string and return for output
     *
     * @param mixed $input
     * @return string
     */
    public function render($input = null)
    {
        if(is_array($input) || is_object($input)){
            return print_r($input, 1);
        }
        return (string)$input;
    }

}