<?php
include_once __DIR__."/../../vendor/autoload.php";
use Controller\TestController;

return [
    [
        "uri"           => '/testing',
        "methods"       => [
            "GET",
            "POST",
        ],
        "controller"    => TestController::class,
        "templateFile"      => "example/view/jsonTemplate.php"
    ],
    [
        "uri"           => '/testing2',
        "methods"       => [
            "GET" => "testing",
            "POST",
        ],
        "controller"    => TestController::class,
        "templateFile"      => "example/view/testTemplate.html"
    ],
];