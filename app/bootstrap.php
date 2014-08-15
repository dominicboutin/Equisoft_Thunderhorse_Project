<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;


require_once __DIR__ ."/vendor/autoload.php";

require PATH_RSC.'/config/dev.php';

// add model here
$paths = array(
    PATH_SRC.'/Model'
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

$app->register(new Silex\Provider\DoctrineServiceProvider());

$app['em'] = $app->share(function ($app) {
    $paths = array(PATH_SRC.'/Model/Entities');
    $isDevMode = false;

    $config = Setup::createConfiguration($isDevMode);
    $driver = new AnnotationDriver(new AnnotationReader(), $paths);

    // registering noop annotation autoloader - allow all annotations by default
    AnnotationRegistry::registerLoader('class_exists');
    $config->setMetadataDriverImpl($driver);

    return EntityManager::create($app['db.options'], $config);
});

$entityManager = $app['em'];//EntityManager::create($params, $config);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));