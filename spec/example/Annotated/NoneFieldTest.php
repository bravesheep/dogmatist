<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Guesser\Annotations as Dogmatist;

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
