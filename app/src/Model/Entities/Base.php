<?php
/**
 * Created by PhpStorm.
 * User: DBoutin
 * Date: 15/08/14
 * Time: 1:01 PM
 */

namespace Model\Entities;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

class Base {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    public function persist(EntityManager $em)
    {
        $em->persist($this);
    }
} 