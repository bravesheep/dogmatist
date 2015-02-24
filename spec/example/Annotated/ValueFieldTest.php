<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Filler\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma()
 */
class ValueFieldTest
{
    /**
     * @var bool
     * @Dogmatist\Value(false)
     */
    public $value;
}
