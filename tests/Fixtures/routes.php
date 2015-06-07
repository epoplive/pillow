<?php
if($composerAutoloader = stream_resolve_include_path("/vendor/autoload.php")){
    include_once $composerAutoloader;
}
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