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
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $smallint;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=2)
     */
    private $decimal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz")
     */
    private $datetimetz;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $float;

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     */
    private $guid;

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

    /**
     * @return int
     */
    public function getSmallint()
    {
        return $this->smallint;
    }

    /**
     * @param int $smallint
     * @return $this
     */
    public function setSmallint($smallint)
    {
        $this->smallint = $smallint;

        return $this;
    }

    /**
     * @return float
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * @param float $decimal
     * @return $this
     */
    public function setDecimal($decimal)
    {
        $this->decimal = $decimal;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime $datetime
     * @return $this
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimetz()
    {
        return $this->datetimetz;
    }

    /**
     * @param \DateTime $datetimetz
     * @return $this
     */
    public function setDatetimetz($datetimetz)
    {
        $this->datetimetz = $datetimetz;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return float
     */
    public function getFloat()
    {
        return $this->float;
    }

    /**
     * @param float $float
     * @return $this
     */
    public function setFloat($float)
    {
        $this->float = $float;

        return $this;
    }

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     * @return $this
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }
}
