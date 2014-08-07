<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ ."/vendor/autoload.php";

/*define( 'PATH_ROOT', dirname( __DIR__ ).'/app' );
define( 'PATH_SRC', PATH_ROOT . '/src' );
define( 'PATH_RSC', PATH_ROOT . '/resources' );

define( 'PATH_CACHE', PATH_RSC . '/cache' );
define( 'PATH_LOCALES', PATH_RSC . '/locales' );

define( 'PATH_PUBLIC', PATH_ROOT . '/web' );
define( 'PATH_VENDOR', PATH_ROOT  );*/

require PATH_RSC.'/config/dev.php';

$paths = array(PATH_SRC.'/Model/Users');

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

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