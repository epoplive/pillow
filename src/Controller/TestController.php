<?php
/**
 * TestController.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Controller;


use Entity\Item;
use Framework\Controller\AbstractBaseController;

class TestController extends AbstractBaseController
{
    public function getAction(){
        return [
            "result" => " goes in here man."
        ];
    }

    public function testing(){
        return [
            "result" => " just ran testing bro"
        ];
    }

    public function testing3(){
        return [
            new Item(["id" => 49, "name"=>"some name here"]),
            new Item(),
            new Item(),
        ];
    }
}