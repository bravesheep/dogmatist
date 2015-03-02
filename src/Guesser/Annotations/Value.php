<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Value implements FieldInterface
{
    /**
     * @var mixed
     * @Required()
     */
    public $value;
}
