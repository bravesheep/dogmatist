<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Fake implements FieldInterface
{
    /**
     * @var string
     * @Required()
     */
    public $type;

    /**
     * @var array
     */
    public $args = [];
}
