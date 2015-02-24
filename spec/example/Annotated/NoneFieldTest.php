<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Filler\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma()
 */
class NoneFieldTest
{
    /**
     * @Dogmatist\None()
     * @var null
     */
    public $none;
}
