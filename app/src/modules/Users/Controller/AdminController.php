<?php

namespace Users\Controller
{
    use Form\Type\NewUserType;
    use Silex\Application;
	use Silex\ControllerProviderInterface;

	class AdminController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$homeController = $app['controllers_factory'];
			$homeController->get("/", array( $this, 'index' ) )->bind( 'admin-users' );
            $homeController->get("/add-user", array( $this, 'new_user' ) )->bind( 'admin-add-user' );
			
			return $homeController;
		}

		public function index( Application $app )
		{
            $repository = $app['em']->getRepository('\Model\Entities\User');
            $users = $repository->findAll();

			return $app['twig']->render('users/index.html.twig', array('users' => $users));
		}

        public function new_user( Application $app )
        {
            $builder = $app['form.factory']->createBuilder('form');

            $form = $builder
                ->add('new_user', new NewUserType())
                ->getForm();

            return $app['twig']->render('users/add_user.html.twig', array('form' => $form->createView()));
        }
	}
}