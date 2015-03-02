<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Constructor implements AnnotationInterface
{
    /**
     * @var \Bravesheep\Dogmatist\Guesser\Annotations\Arg[]
     */
    public $args = [];
}
