<?php

$app->mount('/', new Controller\LoginController() );
$app->mount('/', new Controller\HomeController() );
$app->mount('/', new Controller\FormTestController() );

$app->mount('/admin', new Controller\AdminController() );
$app->mount('/admin/users', new Users\Controller\AdminController() );

$app->mount('/stockcode', new Service\StockcodeController() );

$app->mount('/dashboard', new Controller\DashboardController() );
