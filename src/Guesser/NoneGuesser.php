<?php

namespace Bravesheep\Dogmatist\Guesser;

use Bravesheep\Dogmatist\Builder;

/**
 * A guesser that does absolutely nothing.
 */
class NoneGuesser implements GuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function fill(Builder $builder)
    {
    }
}
