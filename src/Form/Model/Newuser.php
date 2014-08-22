<?php
/**
 * Created by PhpStorm.
 * User: DBoutin
 * Date: 21/08/14
 * Time: 3:55 PM
 */

namespace Form\Model;

use Model\Entities\User;

class NewUser {

    /**
     * @Assert\Type(type="Model\Entities\User")
     */
    protected $user;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
} 