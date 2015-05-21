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
    ],
    [
        "uri"           => '/testing2',
        "methods"       => [
            "GET" => "testing",
            "POST",
        ],
        "controller"    => TestController::class,
    ],
];