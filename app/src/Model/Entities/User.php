<?php
/**
 * Created by PhpStorm.
 * User: KVongsavanthong
 * Date: 06/08/14
 * Time: 2:41 PM
 */

namespace Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 **/
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string")
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", unique = true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", unique = true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    public function getId()
    {
        return $this->id;
    }

    public function getLastname()
    {
        return $this->$lastname;
    }

    public function setLastname($lastname)
    {
        $this->$lastname = $lastname;
    }

    public function getFirstname()
    {
        return $this->$firstname;
    }

    public function setFirstname($firstname)
    {
        $this->$lastname = $firstname;
    }

    public function getEmail()
    {
        return $this->$email;
    }

    public function setEmail($email)
    {
        $this->$email = $email;
    }

    /**
     * @ORM\Column(name="isActive", type="boolean")
     */
    protected $isActive = 1;

    public function __construct()
    {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @ORM\ManyToMany(targetEntity="Roles", inversedBy="Users")
     * @ORM\JoinColumn(name="roles_id_users", referencedColumnName="id")
     *
     */
    private $roles;

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->lastname,
            $this->firstname,
            $this->email,
            $this->isActive
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->lastname,
            $this->firstname,
            $this->email,
            $this->isActive
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
}
