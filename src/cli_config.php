<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

require_once "vendor/autoload.php";

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
$driver = new AnnotationDriver(new AnnotationReader());

// registering noop annotation autoloader - allow all annotations by default
AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);

// Set up database connection data
$conn = array(
    'driver'   => 'pdo_pgsql',
    'host'     => '127.0.0.1',
    'dbname'   => '420lister',
    'user'     => 'postgres',
    'password' => 'password'
);

$entityManager = EntityManager::create($conn, $config);

// We need to provide entityManager to the command line interface
// The CLI interface allows us to submit interact with the database
// for example to update or create the schema
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);