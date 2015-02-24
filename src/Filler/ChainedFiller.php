<?php

namespace Bravesheep\Dogmatist\Filler;

use Bravesheep\Dogmatist\Builder;

class ChainedFiller implements FillerInterface
{
    /**
     * @var FillerInterface[]
     */
    private $fillers = [];

    /**
     * @param FillerInterface[] $fillers
     */
    public function __construct(array $fillers = [])
    {
        foreach ($fillers as $filler) {
            $this->addFiller($filler);
        }
    }

    /**
     * @param FillerInterface $filler
     * @return $this
     */
    public function addFiller(FillerInterface $filler)
    {
        $this->fillers[] = $filler;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fill(Builder $builder)
    {
        foreach ($this->fillers as $filler) {
            $filler->fill($builder);
        }
    }

    /**
     * @return FillerInterface[]
     */
    public function getFillers()
    {
        return $this->fillers;
    }
}
