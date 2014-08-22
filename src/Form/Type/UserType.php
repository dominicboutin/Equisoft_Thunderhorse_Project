<?php
/**
 * Created by PhpStorm.
 * User: DBoutin
 * Date: 12/08/14
 * Time: 1:05 PM
 */

namespace Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'disabled' => true
            ))
            ->add('password', 'repeated', array(
                'type'           => 'password',
                'invalid_message' => 'The password fields must match.',
                'required'       => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'constraints' => array(
                    new NotBlank(),
                ),
            ))
            ->add('roles', 'entity', array(
                'class'    => 'Model\Entities\Role',
                'property' => 'name',
                'expanded' => TRUE,
                'multiple' => TRUE
            ))
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('submit', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Model\Entities\User',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "user";
    }
}