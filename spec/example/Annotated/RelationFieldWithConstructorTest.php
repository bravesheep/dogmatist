<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Filler\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma()
 */
class RelationFieldWithConstructorTest
{
    /**
     * @var RelatedTest
     *
     * @Dogmatist\Relation("Bravesheep\Spec\Annotated\RelatedTest", description=@Dogmatist\Description(
     *  constructor=@Dogmatist\Constructor({
     *      @Dogmatist\Arg(@Dogmatist\Fake("randomNumber"))
     *  })
     * ))
     */
    public $relation;
}
