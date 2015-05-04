<?php

namespace Bravesheep\Dogmatist;

use Bravesheep\Dogmatist\Exception\NoSuchIndexException;

class LinkManager
{
    /**
     * @var Builder[]
     */
    private $builders = [];

    /**
     * @var int[]
     */
    private $counts = [];

    /**
     * @var object[][]
     */
    private $samples = [];

    /**
     * @var Sampler
     */
    private $sampler;

    /**
     * @param Sampler $sampler
     */
    public function __construct(Sampler $sampler)
    {
        $this->sampler = $sampler;
    }

    /**
     * @param Builder $builder
     * @param string  $name
     * @param int     $count
     * @return $this
     */
    public function save(Builder $builder, $name, $count = 1)
    {
        $this->builders[$name] = $builder;
        $this->counts[$name] = $count;
        if (isset($this->samples[$name])) {
            unset($this->samples[$name]);
        }
    }

    /**
     * @param string $name
     * @return Builder
     */
    public function retrieve($name)
    {
        if ($this->has($name)) {
            return $this->builders[$name];
        }
        throw new NoSuchIndexException("There is no builder named {$name}");
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->builders[$name]);
    }

    /**
     * @param string $name
     * @return object[]
     */
    public function samples($name)
    {
        if (!isset($this->samples[$name])) {
            $builder = $this->retrieve($name);
            $this->samples[$name] = $this->sampler->samples($builder, $this->counts[$name]);
        }

        return $this->samples[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasUnlimitedSamples($name)
    {
        if ($this->has($name)) {
            return $this->counts[$name] <= 0;
        }
        return false;
    }

    /**
     * Reset the generated samples.
     */
    public function reset()
    {
        $this->samples = [];
    }
}
