<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__."/vendor/autoload.php";

// replace with file to your own project bootstrap
$paths = array( __DIR__.'/src/Component/Todo/Model/Entity' );
$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '1234',
    'dbname'   => 'jtodo',
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

return ConsoleRunner::createHelperSet($entityManager);