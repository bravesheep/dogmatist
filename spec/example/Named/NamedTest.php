<?php

namespace Bravesheep\Spec\Named;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class NamedTest
{
    public $firstName;

    private $last_name;

    /**
     * @var Collection
     */
    private $addresses;

    public $linkTest;

    public $relationTest;

    public $valueTest;

    public $callbackTest;

    public $noneTest;

    public $selectTest;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param mixed $address
     * @return $this
     */
    public function addAddress($address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * @param mixed $address
     * @return $this
     */
    public function removeAddress($address)
    {
        $this->addresses->removeElement($address);

        return $this;
    }


}
