<?php

namespace Bravesheep\Dogmatist;

use Bravesheep\Dogmatist\Exception\SampleException;
use Bravesheep\Dogmatist\Filler\FillerInterface;
use Faker\Generator as FakerGenerator;

class Dogmatist
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var FillerInterface
     */
    private $filler;

    /**
     * @var LinkManager
     */
    private $linkManager;

    /**
     * @var Sampler
     */
    private $sampler;

    /**
     * @var bool
     */
    private $strict = true;

    /**
     * @return Sampler
     */
    public function getSampler()
    {
        return $this->sampler;
    }

    /**
     * @param Sampler $sampler
     * @return $this
     */
    public function setSampler($sampler)
    {
        $this->sampler = $sampler;

        return $this;
    }

    /**
     * @return LinkManager
     */
    public function getLinkManager()
    {
        return $this->linkManager;
    }

    /**
     * @param LinkManager $linkManager
     * @return $this
     */
    public function setLinkManager($linkManager)
    {
        $this->linkManager = $linkManager;

        return $this;
    }

    /**
     * Set a new faker instance
     * @param FakerGenerator $faker
     * @return $this
     */
    public function setFaker(FakerGenerator $faker)
    {
        $this->faker = $faker;
        return $this;
    }

    /**
     * Retrieve faker instance
     * @return FakerGenerator
     */
    public function getFaker()
    {
        return $this->faker;
    }

    /**
     * @param FillerInterface $filler
     * @return $this
     */
    public function setFiller(FillerInterface $filler)
    {
        $this->filler = $filler;
        return $this;
    }

    /**
     * @return FillerInterface
     */
    public function getFiller()
    {
        return $this->filler;
    }

    /**
     * @param bool $strict
     * @return $this
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * Create a new builder for building objects.
     * @param string $className
     * @return Builder
     */
    public function create($className)
    {
        $builder = new Builder($className, $this);
        $this->getFiller()->fill($builder);
        return $builder;
    }

    /**
     * @param Builder $builder
     * @param string  $name
     * @param int     $count
     * @return $this
     */
    public function save(Builder $builder, $name, $count = 1)
    {
        $this->getLinkManager()->save($builder, $name, $count);
        return $this;
    }

    /**
     * @param string $name
     * @return array|object
     */
    public function sample($name)
    {
        $samples = $this->getLinkManager()->samples($name);
        return $this->getFaker()->randomElement($samples);
    }

    /**
     * @param string $name
     * @param int    $count
     * @return \object[]
     * @throws SampleException
     */
    public function samples($name, $count)
    {
        $samples = $this->getLinkManager()->samples($name);
        $sample_count = count($samples);
        if ($sample_count < $count) {
            throw new SampleException("Wanted to generate {$count} samples, but only {$sample_count} are available");
        }

        return $this->getFaker()->randomElements($samples, $count);
    }

    /**
     * Returns a fresh sample.
     * @param string $name
     * @return array|object
     */
    public function freshSample($name)
    {
        $builder = $this->getLinkManager()->retrieve($name);
        return $this->getSampler()->sample($builder);
    }

    /**
     * Returns fresh samples.
     * @param string $name
     * @param int    $count
     * @return object[]
     */
    public function freshSamples($name, $count)
    {
        $builder = $this->getLinkManager()->retrieve($name);
        return $this->getSampler()->samples($builder, $count);
    }

    /**
     * @param string $name
     * @return Builder
     */
    public function retrieve($name)
    {
        return $this->getLinkManager()->retrieve($name);
    }
}
