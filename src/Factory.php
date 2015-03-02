<?php

namespace Bravesheep\Dogmatist;

use Bravesheep\Dogmatist\Exception\InvalidArgumentException;
use Bravesheep\Dogmatist\Guesser\GuesserInterface;
use Bravesheep\Dogmatist\Guesser\NoneGuesser;
use Faker\Generator as FakerGenerator;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Factory
{
    public static function create(
        $faker = \Faker\Factory::DEFAULT_LOCALE,
        GuesserInterface $filler = null,
        PropertyAccessorInterface $accessor = null
    ) {
        if (is_string($faker)) {
            $faker = \Faker\Factory::create($faker);
        }

        if (!is_object($faker) || !($faker instanceof FakerGenerator)) {
            throw new InvalidArgumentException("Expected instance of \\Faker\\Generator, got " . gettype($faker));
        }

        $dogmatist = new Dogmatist();

        if (null === $filler) {
            $filler = new NoneGuesser();
        }

        if (null === $accessor) {
            $accessor = new PropertyAccessor();
        }

        $sampler = new Sampler($dogmatist, $accessor);
        $linkManager = new LinkManager($sampler);

        $dogmatist->setFaker($faker);
        $dogmatist->setGuesser($filler);
        $dogmatist->setLinkManager($linkManager);
        $dogmatist->setSampler($sampler);

        return $dogmatist;
    }
}
