<?php

$app->mount('/', new Controller\LoginController() );
$app->mount('/', new Controller\HomeController() );
$app->mount('/', new Controller\FormTestController() );

$app->mount('/admin', new Controller\AdminController() );
$app->mount('/admin/users', new Controller\UsersController() );
$app->mount('/admin/roles', new Controller\RolesController() );
