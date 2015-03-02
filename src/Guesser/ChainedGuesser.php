<?php

namespace Bravesheep\Dogmatist\Guesser;

use Bravesheep\Dogmatist\Builder;

class ChainedGuesser implements GuesserInterface
{
    /**
     * @var GuesserInterface[]
     */
    private $fillers = [];

    /**
     * @param GuesserInterface[] $fillers
     */
    public function __construct(array $fillers = [])
    {
        foreach ($fillers as $filler) {
            $this->addFiller($filler);
        }
    }

    /**
     * @param GuesserInterface $filler
     * @return $this
     */
    public function addFiller(GuesserInterface $filler)
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
     * @return GuesserInterface[]
     */
    public function getFillers()
    {
        return $this->fillers;
    }
}
