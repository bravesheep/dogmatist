<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Field implements AnnotationInterface
{
    /**
     * @var \Bravesheep\Dogmatist\Guesser\Annotations\FieldInterface
     *
     * @Required
     */
    public $type;

    /**
     * @var \Bravesheep\Dogmatist\Guesser\Annotations\QuantityInterface
     */
    public $count;

    public function __construct()
    {
        $this->count = new Single();
    }
}
