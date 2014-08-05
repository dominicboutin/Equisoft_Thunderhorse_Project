<?php

namespace CIIN\Controller
{
	use Silex\Application;
	use Silex\ControllerProviderInterface;

	class DashboardController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$dashboardController = $app['controllers_factory'];
			$dashboardController->get("/", array( $this, 'index' ) )->bind( 'dashboard' );
			
			return $dashboardController;
		}

		/**
		 * Login action.
		 *
		 * @param \Silex\Application $app
		 * @return mixed
		 */
		function index( Application $app )
		{
			return $app['twig']->render('graphic.html');
		}
		
	}
}