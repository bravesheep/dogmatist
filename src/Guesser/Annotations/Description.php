<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

/**
 * @Annotation()
 * @Target("ANNOTATION")
 */
class Description implements AnnotationInterface
{
    /**
     * @var \Bravesheep\Dogmatist\Guesser\Annotations\Field[]
     */
    public $fields = [];

    /**
     * @var \Bravesheep\Dogmatist\Guesser\Annotations\Constructor
     */
    public $constructor;
}
