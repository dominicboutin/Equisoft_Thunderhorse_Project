<?php
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

//use Doctrine\ORM\Tools\Setup;
//use Doctrine\ORM\EntityManager;

use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;

require_once __DIR__."../../vendor/autoload.php";

include __DIR__."../../bootstrap.php";

$console = new Application('Silex - Framework', '0.1');

if(isset($app))
    $app->boot();

function DatabaseExist(InputInterface $input, OutputInterface $output, $connection)
{
    try{
        $params = $connection->getParams();
        $name = isset($params['path']) ? $params['path'] : $params['dbname'];
        //unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);

        // Only quote if we don't have a path
        /*if (!isset($params['path'])) {
            $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }*/

        $dbarray = $tmpConnection->getSchemaManager()->listDatabases();
        $name = str_replace('`', '', $name);

        if (isset($dbarray) && in_array($name,$dbarray)) {
            return true;
        }
    } catch (\Exception $e) {
        $output->writeln(sprintf('<error>DatabaseExist</error>'));
        $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
    }
    return false;
}

function TableExist(InputInterface $input, OutputInterface $output, $connection, $table)
{
    try{
        $params = $connection->getParams();
        $name = isset($params['path']) ? $params['path'] : $params['dbname'];
        //unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);

        // Only quote if we don't have a path
        /*if (!isset($params['path'])) {
            $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }*/

        return $tmpConnection->getSchemaManager()->tablesExist($table);

    } catch (\Exception $e) {
        $output->writeln(sprintf('<error>TableExist</error>'));
        $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
    }
    return false;
}

$console
    ->register('assetic:dump')
    ->setDescription('Dumps all assets to the filesystem')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        if (!$app['assetic.enabled']) {
            return false;
        }

        $dumper = $app['assetic.dumper'];
        if (isset($app['twig'])) {
            $dumper->addTwigAssets();
        }
        $dumper->dumpAssets();
        $output->writeln('<info>Dump finished</info>');
    })
;

if (isset($app['cache.path'])) {
    $console
        ->register('cache:clear')
        ->setDescription('Clears the cache')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {

            $cacheDir = $app['cache.path'];
            $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');

            $filesystem = new Filesystem();
            $filesystem->remove($finder);

            $output->writeln(sprintf("%s <info>success</info>", 'cache:clear'));
        });
}

$console
    ->register('doctrine:schema:show')
    ->setDescription('Output schema declaration')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $schema = require PATH_SRC . '/resources/db/schema.php';

        foreach ($schema->toSql($app['db']->getDatabasePlatform()) as $sql) {
            $output->writeln($sql.';');
        }
    })
;

$console
    ->register('doctrine:schema:createDefaultUserRole')
    ->setName('doctrine:schema:createDefaultUserRole')
    ->setDescription('Create the default users: username, admin and role')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $error = false;

        $exist = DatabaseExist($input, $output, $app['db']);
        $current_table = '';

        $connection = $app['db'];
        $params = $connection->getParams();
        $tmpConnection = DriverManager::getConnection($params);

        if($exist) {
            try {

                if(TableExist($input, $output, $app['db'], 'Users')) {
                    $current_table = 'Users';
                    //$user = $app['db']->exec("SELECT * FROM Users WHERE id = 1");
                    $user = $tmpConnection->exec("SELECT * FROM Users WHERE id = 1");
                    $tmpConnection->close();
                    if(!isset($user)){
                        $path = PATH_RSC . '/db/feed/Users.sql';
                        $sql = file_get_contents($path);
                        $app['db']->exec($sql.';');
                    } else
                        $output->writeln(sprintf('<info>Users already exists</info>'));
                }

                if(TableExist($input, $output, $app['db'], 'Roles')) {
                    $current_table = 'Roles';
                    $role = $tmpConnection->exec("SELECT * FROM Roles WHERE id = 1");
                    $tmpConnection->close();
                    if(!isset($role)){
                        $path = PATH_RSC . '/db/feed/Roles.sql';
                        $sql = file_get_contents($path);
                        $app['db']->exec($sql.';');
                    } else
                        $output->writeln(sprintf('<info>Roles already exists</info>'));
                }

                if(TableExist($input, $output, $app['db'], 'users_roles')) {
                    $current_table = 'users_roles';
                    $users_roles = $tmpConnection->exec("SELECT * FROM users_roles WHERE users_id = 1");
                    $tmpConnection->close();
                    if(!isset($users_roles)){
                        $path = PATH_RSC . '/db/feed/users_roles.sql';
                        $sql = file_get_contents($path);
                        $app['db']->exec($sql.';');
                    } else
                        $output->writeln(sprintf('<info>users_roles already exists</info>'));
                }

                $output->writeln(sprintf('<info>Default Users and Roles created.</info>'));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Could not create default Users [%s]</error>', $current_table));
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
                $error = true;
            }
        }
        else {
            $output->writeln(sprintf('<info>Default Users and Roles already present.</info>'));
        }
        return $error ? 1 : 0;
    })
;

$console
    ->register('doctrine:database:drop')
    ->setName('doctrine:database:drop')
    ->setDescription('Drops the configured databases')
    ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command')
    ->addOption('force', null, InputOption::VALUE_NONE, 'Set this parameter to execute this action')
    ->setHelp(
        <<<EOT
The <info>doctrine:database:drop</info> command drops the default connections
database:

<info>php app/console doctrine:database:drop</info>

The --force parameter has to be used to actually drop the database.

You can also optionally specify the name of a connection to drop the database
for:

<info>php app/console doctrine:database:drop --connection=default</info>

<error>Be careful: All data in a given database will be lost when executing
this command.</error>
EOT
    )
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $connection = $app['db'];

        $params = $connection->getParams();

        $name = isset($params['path']) ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);

        if (!$name) {
            throw new \InvalidArgumentException("Connection does not contain a 'path' or 'dbname' parameter and cannot be dropped.");
        }

        if ($input->getOption('force')) {
            // Only quote if we don't have a path
            if (!isset($params['path'])) {
                $name = $connection->getDatabasePlatform()->quoteSingleIdentifier($name);
            }

            try {
                $connection->getSchemaManager()->dropDatabase($name);
                $output->writeln(sprintf('<info>Dropped database for connection named <comment>%s</comment></info>', $name));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Could not drop database for connection named <comment>%s</comment></error>', $name));
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

                return 1;
            }
        } else {
            $output->writeln('<error>ATTENTION:</error> This operation should not be executed in a production environment.');
            $output->writeln('');
            $output->writeln(sprintf('<info>Would drop the database named <comment>%s</comment>.</info>', $name));
            $output->writeln('Please run the operation with --force to execute');
            $output->writeln('<error>All data will be lost!</error>');

            return 2;
        }
    })
;

$console
    ->register('doctrine:database:create')
    ->setDescription('Creates the configured databases')
    ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command')
    ->setHelp(
        <<<EOT
The <info>doctrine:database:create</info> command creates the default
connections database:

<info>php app/console doctrine:database:create</info>

You can also optionally specify the name of a connection to create the
database for:

<info>php app/console doctrine:database:create --connection=default</info>
EOT
    )
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $connection = $app['db'];

        //$params = $app['db.options'];

        $params = $connection->getParams();
        $name = isset($params['path']) ? $params['path'] : $params['dbname'];

        unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);

        // Only quote if we don't have a path
        if (!isset($params['path'])) {
            $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }

        $error = false;
        try {
            $dbarray = $tmpConnection->getSchemaManager()->listDatabases();

            $name = str_replace('`', '', $name);

            if (isset($dbarray) && in_array($name,$dbarray)) {
                $output->writeln(sprintf('<info>Table for connection named <comment>%s</comment> exist</info>', $name));
            }
            else {
                $tmpConnection->getSchemaManager()->createDatabase($name);
                $output->writeln(sprintf('<info>Created database for connection named <comment>%s</comment></info>', $name));
            }

        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Could not create database for connection named <comment>%s</comment></error>', $name));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $error = true;
        }

        $tmpConnection->close();

        return $error ? 1 : 0;
    })
;


$console->setHelperSet($helperSet);
Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands($console);

return $console;
