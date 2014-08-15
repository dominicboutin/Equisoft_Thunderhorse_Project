<?php

namespace Users\Controller
{
    use Form\Type\UserType;
    use Model\Entities\User;
    use Silex\Application;
	use Silex\ControllerProviderInterface;
    use Symfony\Component\Form\FormError;

    class AdminController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$homeController = $app['controllers_factory'];
			$homeController->get("/", array( $this, 'index' ) )->bind( 'admin-users' );
            $homeController->match("/add-user", array( $this, 'new_user' ) )->bind( 'admin-add-user' );
			
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
            $user = new User();
            $user->setFirstname('lol');
            $user->setLastname('lol2');
            $builder = $app['form.factory']->createBuilder(new UserType(), $user);

            $form = $builder
                ->getForm();

            $request = $app["request"];

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    print_r($user);
                    $app['session']->getFlashBag()->add('success', 'The form is valid');
                } else {
                    $form->addError(new FormError('This is a global error'));
                    $app['session']->getFlashBag()->add('info', 'The form is bound, but not valid');
                }
            }

            return $app['twig']->render('users/add_user.html.twig', array('form' => $form->createView()));
        }
	}
}