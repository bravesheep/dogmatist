<?php

namespace Bravesheep\Dogmatist\Guesser;

use Bravesheep\Dogmatist\Builder;

interface GuesserInterface
{
    /**
     * @param Builder $builder
     */
    public function fill(Builder $builder);
}
