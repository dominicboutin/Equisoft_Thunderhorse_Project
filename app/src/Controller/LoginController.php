<?php

namespace Controller
{
	use Silex\Application;
	use Silex\ControllerProviderInterface;

	class LoginController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$loginController = $app['controllers_factory'];
			$loginController->get("/login", array( $this, 'login' ) )->bind( 'login' );
			$loginController->get("/logout", array( $this, 'logout' ) )->bind( 'logout' );
			return $loginController;
		}

		/**
		 * Login action.
		 *
		 * @param \Silex\Application $app
		 * @return mixed
		 */
		function login( Application $app )
		{
			$form = $app['form.factory']->createBuilder('form')
			->add(
				'username',
				'text',
				array(
					'label' => 'Username',
					'data' => $app['session']->get('_security.last_username')
				)
			)
			->add('password', 'password', array('label' => 'Password'))
			->getForm()
			;
			 //echo $token;

            $token = $app['security']->getToken();
            if (null !== $token) {
                $user = $token->getUser();
            }
			 
			$request = $app["request"];
			return $app['twig']->render('login.html.twig', array(
				'form'  => $form->createView(),
				'error' => $app['security.last_error']($request),
			));			
		}

		/**
		 * Logout action.
		 *
		 * @param \Silex\Application $app
		 * @return type
		 */
		function logout( Application $app )
		{
			$token = $app['security']->getToken();			
			 echo $token;
			 
			$app['session']->clear();
			return $app->redirect($app['url_generator']->generate('login'));		
		}
	}
}