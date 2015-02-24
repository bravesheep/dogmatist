<?php

namespace Bravesheep\Dogmatist\Filler\Annotations;

/**
 * @Annotation()
 * @Target("ANNOTATION")
 */
class Description implements AnnotationInterface
{
    /**
     * @var \Bravesheep\Dogmatist\Filler\Annotations\Field[]
     */
    public $fields = [];

    /**
     * @var \Bravesheep\Dogmatist\Filler\Annotations\Constructor
     */
    public $constructor;
}
