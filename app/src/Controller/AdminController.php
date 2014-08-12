<?php

namespace Controller
{
	use Silex\Application;
	use Silex\ControllerProviderInterface;

	class AdminController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$homeController = $app['controllers_factory'];
			$homeController->get("/", array( $this, 'index' ) )->bind( 'admin' );
			
			return $homeController;
		}

		public function index( Application $app )
		{
			return $app['twig']->render('admin-index.html.twig');
		}
	}
}