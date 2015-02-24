<?php

namespace Bravesheep\Dogmatist\Filler;

use Bravesheep\Dogmatist\Builder;

interface FillerInterface
{
    /**
     * @param Builder $builder
     */
    public function fill(Builder $builder);
}
