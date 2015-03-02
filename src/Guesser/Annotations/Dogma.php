<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

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
