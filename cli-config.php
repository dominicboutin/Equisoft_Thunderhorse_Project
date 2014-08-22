<?php
// cli-config.php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once "vendor/autoload.php";
require_once "bootstrap.php";

//$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
//    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
//    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
//));
return ConsoleRunner::createHelperSet($entityManager);