<?php

namespace Bravesheep\Dogmatist\Guesser\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Link implements FieldInterface
{
    /**
     * @var string
     * @Required()
     */
    public $target;
}
