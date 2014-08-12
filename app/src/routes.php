<?php

$app->mount('/', new Controller\LoginController() );
$app->mount('/', new Controller\HomeController() );
$app->mount('/', new Controller\FormTestController() );
//$app->mount('/users', new CIIN\Users\Controller\UsersController() );

$app->mount('/stockcode', new Service\StockcodeController() );

$app->mount('/dashboard', new Controller\DashboardController() );
