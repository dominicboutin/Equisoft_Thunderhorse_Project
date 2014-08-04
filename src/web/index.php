<?php
define( 'PATH_ROOT', dirname( __DIR__ ) );
define( 'PATH_SRC', PATH_ROOT . '/resources' );

define( 'PATH_CACHE', PATH_SRC . '/cache' );
define( 'PATH_LOCALES', PATH_SRC . '/locales' );

define( 'PATH_PUBLIC', PATH_ROOT . '/web' );
define( 'PATH_VENDOR', PATH_ROOT  );

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

switch (strtolower($_SERVER["SERVER_NAME"])) {
    default:
    case "localhost.silex.poc.equisoft.com":
        // Load Dev Config
        Symfony\Component\Debug\Debug::enable();
        require PATH_SRC.'/config/dev.php';
        break;

    case "silex.poc.equisoft.com":
        // Load Prod Config
        require PATH_SRC.'/config/prod.php';
        break;
}

require PATH_SRC . '/app.php';

require PATH_SRC . '/controllers.php';

$app['http_cache']->run();
