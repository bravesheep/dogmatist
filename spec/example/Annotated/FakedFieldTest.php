<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Guesser\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma()
 */
class FakedFieldTest
{
    /**
     * @Dogmatist\Fake("name")
     * @var string
     */
    public $faked;
}
