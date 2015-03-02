<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

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
     * @var \Bravesheep\Dogmatist\Guesser\Annotations\Description
     * @Required()
     */
    public $description;
}
