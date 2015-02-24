<?php

namespace Bravesheep\Dogmatist\Filler\Annotations;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class Constructor implements AnnotationInterface
{
    /**
     * @var \Bravesheep\Dogmatist\Filler\Annotations\Arg[]
     */
    public $args = [];
}
