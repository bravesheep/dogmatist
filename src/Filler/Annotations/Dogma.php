<?php

namespace Bravesheep\Dogmatist\Filler\Annotations;

/**
 * @Annotation()
 * @Target("CLASS")
 */
class Dogma implements AnnotationInterface
{
    /**
     * @var bool
     */
    public $strict = true;
}
