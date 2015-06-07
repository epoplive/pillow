<?php
error_reporting(E_ALL);
set_include_path(__DIR__.':'.realpath(__DIR__."/../../../").':'.get_include_path());
if(!$composerAutoloader = stream_resolve_include_path("vendor/autoload.php")){
    echo "Composer autoloader not found: $composerAutoloader" . PHP_EOL;
    echo "Please issue 'composer install' and try again." . PHP_EOL;
    echo get_include_path().PHP_EOL;
    exit(1);
}
require $composerAutoloader;