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
        var_dump("some shit");
    }
}