<?php

$app->mount('/', new Controller\LoginController() );
$app->mount('/', new Controller\HomeController() );
$app->mount('/', new Controller\FormTestController() );

$app->mount('/stockcode', new Service\StockcodeController() );

$app->mount('/dashboard', new Controller\DashboardController() );

/**
 * example:
$app['post_model'] = $app->share(
	function( $app )
	{
		return new Model\PostModel( $app['db'] );
	}
);
$app->mount( '/post', new Controller\PostController( $app['post_model'] ) );
*/
?>
