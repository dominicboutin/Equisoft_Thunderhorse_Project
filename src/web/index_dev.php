<?php
define( 'PATH_ROOT', dirname( __DIR__ ) );
define( 'PATH_SRC', PATH_ROOT . '/resources' );

define( 'PATH_CACHE', PATH_SRC . '/cache' );
define( 'PATH_LOCALES', PATH_SRC . '/locales' );

define( 'PATH_PUBLIC', PATH_ROOT . '/web' );
define( 'PATH_VENDOR', PATH_ROOT  );

require_once __DIR__.'/../vendor/autoload.php';

Symfony\Component\Debug\Debug::enable();

$app = new Silex\Application();

require PATH_SRC.'/config/dev.php';
require PATH_SRC . '/app.php';

require PATH_SRC . '/controllers.php';


$app->run();
