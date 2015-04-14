<?php

namespace Bravesheep\Dogmatist;

class ReplacableLink 
{
    /**
     * @var object|array
     */
    public $value;

    /**
     * @var string|int
     */
    public $field;

    /**
     * @param object|array $value
     * @param string|int   $field
     */
    public function __construct($value, $field)
    {
        $this->value = $value;
        $this->field = $field;
    }
}
