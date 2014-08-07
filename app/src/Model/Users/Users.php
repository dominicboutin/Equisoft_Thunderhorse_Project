<?php
/**
 * Created by PhpStorm.
 * User: KVongsavanthong
 * Date: 06/08/14
 * Time: 2:41 PM
 */
// Model/Users.php
namespace Model\Users;

use Doctrine\ORM\Mapping as ORM;
/**
 * @Entity
 * @Table(name="Users")
 **/
class Users {
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $lastname;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $firstname;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $email;

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
}
