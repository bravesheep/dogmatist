<?php

namespace Bravesheep\Spec\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class BasicFieldTest
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $string;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $bool;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $int;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBool()
    {
        return $this->bool;
    }

    /**
     * @param boolean $bool
     * @return $this
     */
    public function setBool($bool)
    {
        $this->bool = $bool;

        return $this;
    }

    /**
     * @return int
     */
    public function getInt()
    {
        return $this->int;
    }

    /**
     * @param int $int
     * @return $this
     */
    public function setInt($int)
    {
        $this->int = $int;

        return $this;
    }
}
