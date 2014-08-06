<?php
/**
 * Created by PhpStorm.
 * User: DBoutin
 * Date: 05/08/14
 * Time: 1:41 PM
 */

namespace CIIN\Users\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

class UsersController implements ControllerProviderInterface
{
    public function connect( Application $app )
    {
        $loginController = $app['controllers_factory'];
        $loginController->get("/", array( $this, 'test' ) )->bind( 'users' );
        return $loginController;
    }

    function test( Application $app )
    {
        $request = $app["request"];
        return $app['twig']->render('test.html.twig', array(
            'error' => $app['security.last_error']($request),
        ));
    }
} 