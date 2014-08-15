<?php

namespace Controller
{
    use Form\Type\UserType;
    use Model\Entities\User;
    use Silex\Application;
	use Silex\ControllerProviderInterface;
    use Symfony\Component\Form\FormError;

    class UsersController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$homeController = $app['controllers_factory'];
			$homeController->get("/", array( $this, 'index' ) )->bind( 'admin-users' );
            $homeController->get("/user/{id}", array( $this, 'user' ) )->bind( 'admin-user' );
            $homeController->match("/add-user", array( $this, 'add_user' ) )->bind( 'admin-add-user' );
			
			return $homeController;
		}

		public function index( Application $app )
		{
            $repository = $app['em']->getRepository('\Model\Entities\User');
            $users = $repository->findAll();

			return $app['twig']->render('users/index.html.twig', array('users' => $users));
		}

        public function user( Application $app, $id )
        {
            $repository = $app['em']->getRepository('\Model\Entities\User');
            $users = $repository->findById($id);

            if (count($users) > 0)
            {
                $user = $users[0];
                return $app['twig']->render('users/user.html.twig', array('user' => $user));
            }
            else
            {
                $app['session']->getFlashBag()->add('warning', 'User do not exist');
                return $app->redirect('/admin/users/');
            }
        }

        public function add_user( Application $app )
        {
            $user = new User();
            $builder = $app['form.factory']->createBuilder(new UserType(), $user);

            $form = $builder
                ->getForm();

            $request = $app["request"];

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $user->persist($app['em']);
                    $app['em']->flush();
                    $app['session']->getFlashBag()->add('success', 'User was created');
                } else {
                    $form->addError(new FormError('This is a global error'));
                    $app['session']->getFlashBag()->add('info', 'Error, the user could not be created');
                }
            }

            return $app['twig']->render('users/add_user.html.twig', array('form' => $form->createView()));
        }
	}
}