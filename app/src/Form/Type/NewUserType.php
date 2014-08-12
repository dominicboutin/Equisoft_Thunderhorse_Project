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

class NewUserType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fist_name')
            ->add('last_name')
            ->add('username')
            ->add('password', 'password')
            ->add('submit', 'submit');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "new_user";
    }
}