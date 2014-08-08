<?php

namespace SKE\Composer;

class Script
{
    public static function Install()
    {
        chmod('resources/cache', 0777);
        chmod('resources/log', 0777);
        chmod('web/assets', 0777);
        chmod('console', 0500);
        exec('php console assetic:dump');
        exec('php console doctrine:database:create');
        exec('php console orm:schema-tool:update --force');
        exec('php console doctrine:schema:createDefaultUserRole');

    }

    public static function Update()
    {
        exec('php console assetic:dump');
        exec('php console doctrine:database:create');
        exec('php console orm:schema-tool:update --force');
        exec('php console doctrine:schema:createDefaultUserRole');
    }
}
