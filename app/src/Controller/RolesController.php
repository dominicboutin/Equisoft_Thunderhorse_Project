<?php

namespace Controller
{
    use Form\Type\NewUserType;
    use Form\Type\UserType;
    use Model\Entities\User;
    use Silex\Application;
	use Silex\ControllerProviderInterface;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormError;

    class RolesController implements ControllerProviderInterface
	{
		public function connect( Application $app )
		{
			$rolesController = $app['controllers_factory'];
            $rolesController->get("/", array( $this, 'users' ) )->bind( 'admin-roles' );
            $rolesController->match("/role/{id}", array( $this, 'role' ) )->bind( 'admin-role' );
            $rolesController->match("/new", array( $this, 'new_role' ) )->bind( 'admin-new-role' );
			
			return $rolesController;
		}

		public function users( Application $app )
		{
            $repository = $app['em']->getRepository('\Model\Entities\Role');
            $roles = $repository->findAll();

			return $app['twig']->render('admin/roles/roles.html.twig', array('roles' => $roles));
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
                return $app->redirect('/admin/roles/');
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

            return $app['twig']->render('admin/roles/role.html.twig', array('form' => $form->createView()));
        }

        public function new_user( Application $app )
        {
            $user = new User();
            $builder = $app['form.factory']->createBuilder(new NewUserType(), $user);

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

            return $app['twig']->render('admin/roles/new_role.html.twig', array('form' => $form->createView()));
        }
	}
}