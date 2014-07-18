<?php
define( 'PATH_ROOT', dirname( __DIR__ ) );
define( 'PATH_SRC', PATH_ROOT . '/src' );

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

switch (strtolower($_SERVER["SERVER_NAME"])) {
    default:
    case "localhost.silex.poc.equisoft.com":
        // Load Dev Config
        Symfony\Component\Debug\Debug::enable();
        require __DIR__.'/../resources/config/dev.php';
        break;
    case "silex.poc.equisoft.com":
        // Load Prod Config
        require __DIR__.'/../resources/config/prod.php';
        break;
}

require __DIR__.'/../resources/config/prod.php';
require __DIR__.'/../src/app.php';

require __DIR__.'/../src/controllers.php';

$app['http_cache']->run();
