<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ ."/vendor/autoload.php";

require PATH_RSC.'/config/dev.php';

// add model here
$paths = array(
    PATH_SRC.'/Model/Users',
    PATH_SRC.'/Model/Roles'
);

$isDevMode = false;
$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);

$params = $app['db.options'];
/*
$params = array(
    'driver'  => 'pdo_mysql',
    'user'   => 'devuser',
    'password' => 'devuser',
    'dbname'  => 'test',
);*/

$entityManager = EntityManager::create($params, $config);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));