<?php
/**
 * JSONTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;


use Zend\Json\Json;

class SimpleJSONTemplateView extends AbstractBaseTemplateView implements TemplateViewInterface
{
    const STATUS_SUCCESS = "success";
    const STATUS_FAIL = "fail";
    const STATUS_ERROR = "error";


    protected $template = [
        "status"        => self::STATUS_SUCCESS,
        "data"          => [],
        "messages"      => null,
        "errors"        => null,
    ];

    /**
     * Render the view into a string and return for output
     *
     * @param null|Array $input
     * @return string
     */
    public function render(Array $input = null)
    {
        $output = (object)$this->template;
        if(isset($input["status"])) {
            $output->status = $input["status"];
        } else {
            unset($output->status);
        }

        if(isset($input["errors"])) {
            $output->errors = $input["errors"];
        } else {
            unset($output->errors);
        }

        if(isset($input["data"])) {
            $output->data = $input["data"];
        } else {
            unset($output->data);
        }

        if(isset($input["messages"])) {
            $output->messages = $input["messages"];
        } else {
            unset($output->messages);
        }
        return Json::encode($input);
    }

}