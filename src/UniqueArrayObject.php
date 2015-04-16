<?php

namespace Bravesheep\Dogmatist;

class UniqueArrayObject extends \ArrayObject
{
    static $previous = 0;

    private $id;

    public function __construct()
    {
        parent::__construct();
        self::$previous += 1;
        $this->id = self::$previous;
    }

    public function getId()
    {
        return $this->id;
    }
}
