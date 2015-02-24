<?php

namespace Bravesheep\Dogmatist\Filler\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Select implements FieldInterface
{
    /**
     * @var array
     * @Required()
     */
    public $values;
}
