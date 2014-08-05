<?php

$app->mount('/', new CIIN\Controller\LoginController() );
$app->mount('/', new CIIN\Controller\HomeController() );
$app->mount('/', new CIIN\Controller\FormTestController() );
$app->mount('/users', new CIIN\Users\Controller\UsersController() );

$app->mount('/stockcode', new CIIN\Service\StockcodeController() );

$app->mount('/dashboard', new CIIN\Controller\DashboardController() );
