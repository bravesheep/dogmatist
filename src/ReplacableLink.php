<?php

namespace Bravesheep\Dogmatist;

class ReplacableLink 
{
    /**
     * @var object|array
     */
    public $value;

    /**
     * @var mixed[]
     */
    public $fields;

    /**
     * @param object|array $value
     * @param mixed[]      $fields
     * @param bool         $constructor
     */
    public function __construct($value, array $fields)
    {
        $this->value = $value;
        $this->fields = $fields;
    }
}
