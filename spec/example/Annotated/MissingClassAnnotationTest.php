<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Guesser\Annotations as Dogmatist;

class MissingClassAnnotationTest
{
    /**
     * @Dogmatist\Fake("name")
     * @var string
     */
    public $faked;
}
