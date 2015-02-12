<?php

namespace Bravesheep\Dogmatist\Filler;

use Bravesheep\Dogmatist\Builder;

interface FillerInterface
{
    public function fill(Builder $builder);
}
