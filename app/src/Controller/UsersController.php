<?php

namespace Controller
{
    use Form\Type\UserType;
    use Model\Entities\User;
    use Silex\Application;
	use Silex\ControllerProviderInterface;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormError;

    class UsersController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$homeController = $app['controllers_factory'];
			$homeController->get("/", array( $this, 'users' ) )->bind( 'admin-users' );
            $homeController->match("/user/{id}", array( $this, 'user' ) )->bind( 'admin-user' );
            $homeController->match("/new", array( $this, 'new_user' ) )->bind( 'admin-new-user' );
			
			return $homeController;
		}

		public function users( Application $app )
		{
            $repository = $app['em']->getRepository('\Model\Entities\User');
            $users = $repository->findAll();

			return $app['twig']->render('admin/users/users.html.twig', array('users' => $users));
		}

        /**
         * @param Application $app
         * @param             $id
         * @return \Symfony\Component\HttpFoundation\RedirectResponse
         */
        public function user( Application $app, $id )
        {
            $repository = $app['em']->getRepository('\Model\Entities\User');
            $user = $repository->findOneById($id);

            if ( ! $user)
            {
                $app['session']->getFlashBag()->add('warning', 'User do not exist');
                return $app->redirect('/admin/users/');
            }

            $request = $app["request"];

            /** @var FormBuilderInterface $builder */
            $builder = $app['form.factory']->createBuilder(new UserType(), $user);

            $form = $builder->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted())
            {
                if ($form->isValid())
                {
                    $app['em']->persist($user);
                    $app['em']->flush();
                    $app['session']->getFlashBag()->add('success', 'User was saved');
                }
                else
                {
                    $form->addError(new FormError('This is a global error'));
                    $app['session']->getFlashBag()->add('info', 'Error, the user could not be saved');
                }
            }

            return $app['twig']->render('admin/users/user.html.twig', array('form' => $form->createView()));
        }

        public function new_user( Application $app )
        {
            $user = new User();
            $builder = $app['form.factory']->createBuilder(new UserType(), $user);

            $form = $builder->getForm();

            $request = $app["request"];

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $app['em']->persist($user);
                    $app['em']->flush();
                    $app['session']->getFlashBag()->add('success', 'User was created');
                } else {
                    $form->addError(new FormError('This is a global error'));
                    $app['session']->getFlashBag()->add('info', 'Error, the user could not be created');
                }
            }

            return $app['twig']->render('admin/users/new_user.html.twig', array('form' => $form->createView()));
        }
	}
}