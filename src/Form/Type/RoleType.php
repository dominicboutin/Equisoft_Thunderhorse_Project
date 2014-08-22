<?php
/**
 * Created by PhpStorm.
 * User: DBoutin
 * Date: 12/08/14
 * Time: 1:05 PM
 */

namespace Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleType extends AbstractType {

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Model\Entities\Role',
            'property' => 'name'
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'role';
    }
}