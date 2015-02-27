<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Filler\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma(strict=true)
 */
class ValueFieldTest
{
    /**
     * @var bool
     * @Dogmatist\Value(false)
     */
    public $value;
}
