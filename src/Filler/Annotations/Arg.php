<?php

namespace Bravesheep\Dogmatist\Filler\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class Arg implements AnnotationInterface
{
    /**
     * @var \Bravesheep\Dogmatist\Filler\Annotations\FieldInterface
     *
     * @Required
     */
    public $type;

    /**
     * @var \Bravesheep\Dogmatist\Filler\Annotations\QuantityInterface
     */
    public $count;

    public function __construct()
    {
        $this->count = new Single();
    }
}
