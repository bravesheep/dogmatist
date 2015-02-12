<?php

namespace Bravesheep\Dogmatist;

use Bravesheep\Dogmatist\Exception\InvalidArgumentException;
use Bravesheep\Dogmatist\Filler\FillerInterface;
use Bravesheep\Dogmatist\Filler\PhpDocFiller;
use Faker\Generator as FakerGenerator;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Factory
{
    public static function create(
        $faker = \Faker\Factory::DEFAULT_LOCALE,
        FillerInterface $filler = null,
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
            $filler = new PhpDocFiller();
        }

        if (null === $accessor) {
            $accessor = new PropertyAccessor();
        }

        $sampler = new Sampler($dogmatist, $accessor);
        $linkManager = new LinkManager($sampler);

        $dogmatist->setFaker($faker);
        $dogmatist->setFiller($filler);
        $dogmatist->setLinkManager($linkManager);
        $dogmatist->setSampler($sampler);

        return $dogmatist;
    }
}
