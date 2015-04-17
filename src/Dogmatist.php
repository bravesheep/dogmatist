<?php

namespace Bravesheep\Dogmatist;

use Bravesheep\Dogmatist\Exception\SampleException;
use Bravesheep\Dogmatist\Guesser\GuesserInterface;
use Faker\Generator as FakerGenerator;

class Dogmatist
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var GuesserInterface
     */
    private $guesser;

    /**
     * @var LinkManager
     */
    private $linkManager;

    /**
     * @var Sampler
     */
    private $sampler;

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
     * @param GuesserInterface $guesser
     * @return $this
     */
    public function setGuesser(GuesserInterface $guesser)
    {
        $this->guesser = $guesser;
        return $this;
    }

    /**
     * @return GuesserInterface
     */
    public function getGuesser()
    {
        return $this->guesser;
    }

    /**
     * Create a new builder for building objects.
     * @param string $className
     * @return Builder
     */
    public function create($className)
    {
        $builder = new Builder($className, $this);
        $this->getGuesser()->fill($builder);
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
     * @param string|Builder $name
     * @return array|object
     */
    public function sample($name)
    {
        if ($name instanceof Builder) {
            return $this->getSampler()->sample($name);
        }

        if ($this->getLinkManager()->hasUnlimitedSamples($name)) {
            return $this->getSampler()->sample($this->getLinkManager()->retrieve($name));
        }

        $samples = $this->getLinkManager()->samples($name);
        return $this->getFaker()->randomElement($samples);
    }

    /**
     * @param string|Builder $name
     * @param int            $count
     * @return object[]
     * @throws SampleException
     */
    public function samples($name, $count)
    {
        if ($name instanceof Builder) {
            return $this->getSampler()->samples($name, $count);
        }

        if ($this->getLinkManager()->hasUnlimitedSamples($name)) {
            return $this->getSampler()->samples($this->getLinkManager()->retrieve($name), $count);
        }

        $samples = $this->getLinkManager()->samples($name);
        $sample_count = count($samples);
        if ($sample_count < $count) {
            throw new SampleException(
                "Wanted to generate {$count} samples, but only {$sample_count} are available"
            );
        }

        return $this->getFaker()->randomElements($samples, $count);
    }

    /**
     * Returns a fresh sample.
     * @param string|Builder $name
     * @return array|object
     */
    public function freshSample($name)
    {
        if ($name instanceof Builder) {
            return $this->getSampler()->sample($name);
        }
        $builder = $this->getLinkManager()->retrieve($name);
        return $this->getSampler()->sample($builder);
    }

    /**
     * Returns fresh samples.
     * @param string|Builder $name
     * @param int            $count
     * @return object[]
     */
    public function freshSamples($name, $count)
    {
        if ($name instanceof Builder) {
            return $this->getSampler()->samples($name, $count);
        }

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

    /**
     * Retrieve a cloned copy of the specified builder.
     * @param string|Builder $builder
     * @param string         $type The new type of the cloned builder.
     * @return Builder
     */
    public function copy($builder, $type = null)
    {
        if (!($builder instanceof Builder)) {
            $builder = $this->retrieve($builder);
        }
        return $builder->copy($type);
    }
}
