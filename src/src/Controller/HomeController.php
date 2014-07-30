<?php

namespace Controller
{
	use Silex\Application;
	use Silex\ControllerProviderInterface;

	class HomeController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$homeController = $app['controllers_factory'];
			$homeController->get("/", array( $this, 'index' ) )->bind( 'homepage' );
			
			return $homeController;
		}

		public function index( Application $app )
		{		
			 $token = $app['security']->getToken();			
			 //echo $token;
			// if ( empty( $token ) && !$token->isAuthenticated() )
			// {
				// return $app->redirect( $app['url_generator']->generate( 'login' ) );
			// }
			
			$app['session']->getFlashBag()->add('warning', 'Warning flash message');
			$app['session']->getFlashBag()->add('info', 'Info flash message');
			$app['session']->getFlashBag()->add('success', 'Success flash message');
			$app['session']->getFlashBag()->add('error', 'Error flash message');
			
			return $app['twig']->render('index.html.twig');
		}
	}
}