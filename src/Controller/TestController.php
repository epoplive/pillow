<?php
/**
 * TestController.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Controller;


use Framework\Controller\AbstractBaseController;

class TestController extends AbstractBaseController
{
    public function get(){
        return [
            "result" => " goes in here man."
        ];
    }
}