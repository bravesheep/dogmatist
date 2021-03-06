<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Multiple implements QuantityInterface
{
    /**
     * @var int
     * @Required()
     */
    public $min;

    /**
     * @var int
     * @Required()
     */
    public $max;
}
