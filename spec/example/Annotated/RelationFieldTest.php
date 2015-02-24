<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Filler\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma()
 */
class RelationFieldTest
{
    /**
     * @var array
     *
     * @Dogmatist\Relation("array", description=@Dogmatist\Description({
     *  @Dogmatist\Field(@Dogmatist\Fake("randomNumber"))
     * }))
     * @Dogmatist\Multiple(min=10, max=20)
     */
    public $relation;
}
