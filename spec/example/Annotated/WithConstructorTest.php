<?php

namespace Bravesheep\Spec\Annotated;

use Bravesheep\Dogmatist\Guesser\Annotations as Dogmatist;

/**
 * @Dogmatist\Dogma(strict=false)
 */
class WithConstructorTest
{
    /**
     * @var array
     */
    public $items;

    /**
     * @var int
     */
    public $number;

    /**
     * @Dogmatist\Constructor({
     *  @Dogmatist\Arg(@Dogmatist\Link("another"), count=@Dogmatist\Multiple(min=2, max=10)),
     *  @Dogmatist\Arg(@Dogmatist\Fake("randomNumber"))
     * })
     */
    public function __construct(array $items, $number)
    {
        $this->items = $items;
        $this->number = $number;
    }
}
