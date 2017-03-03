<?php
include_once __DIR__."/../../vendor/autoload.php";
use Controller\TestController;
use Framework\View\TemplateView\DoctrineAnnotationTemplateView;

return [
    [
        "uri"           => '/testing',
        "methods"       => [
            "GET",
            "POST",
        ],
        "controller"    => TestController::class,
        "view"          => "example/view/jsonTemplate.php"
    ],
    [
        "uri"           => '/testing2',
        "methods"       => [
            "GET" => "testing",
            "POST",
        ],
        "controller"    => TestController::class,
        "view"          => "example/view/testTemplate.html"
    ],
    [
        "uri"           => '/testing3',
        "methods"       => [
            "GET" => "testing3",
            "POST",
        ],
        "controller"    => TestController::class,
        "viewClass"     => DoctrineAnnotationTemplateView::class,
    ],
];