<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Filler\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma()
 */
class SelectFieldTest
{
    /**
     * @var string
     * @Dogmatist\Select({"x", "y"})
     */
    public $select;
}
