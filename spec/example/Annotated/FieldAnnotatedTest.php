<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Guesser\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma()
 */
class FieldAnnotatedTest 
{
    /**
     * @var array
     * @Dogmatist\Field(@Dogmatist\Fake("name"), count=@Dogmatist\Multiple(min=10, max=20))
     */
    public $field;
}
