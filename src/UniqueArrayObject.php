<?php

namespace Bravesheep\Dogmatist;

class UniqueArrayObject extends \ArrayObject
{
    static $previous = 0;

    private $id;

    public $parent;

    public function __construct(UniqueArrayObject $parent = null)
    {
        parent::__construct();
        self::$previous += 1;
        $this->id = self::$previous;
        $this->parent = $parent;
    }

    public function getId()
    {
        return $this->id;
    }
}
