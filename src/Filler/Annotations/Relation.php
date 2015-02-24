<?php

namespace Bravesheep\Dogmatist\Filler\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Relation implements FieldInterface
{
    /**
     * @var string
     * @Required()
     */
    public $type;

    /**
     * @var \Bravesheep\Dogmatist\Filler\Annotations\Description
     * @Required()
     */
    public $description;
}
