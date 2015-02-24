<?php

namespace Bravesheep\Spec\Annotated;

class MissingClassAnnotationTest
{
    /**
     * @Dogmatist\Fake("name")
     * @var string
     */
    public $faked;
}
