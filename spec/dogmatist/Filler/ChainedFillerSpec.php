<?php

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Filler\ChainedFiller;
use Bravesheep\Dogmatist\Filler\FillerInterface;
use kahlan\plugin\Stub;

describe("ChainedFiller", function () {
    it("should be constructed without any fillers by default", function () {
        $filler = new ChainedFiller();
        $dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $filler);
        expect($dogmatist->getFiller())->toBe($filler);
        expect($filler->getFillers())->toHaveLength(0);
    });

    it("should add all fillers provided in the constructor", function () {
        $filler1 = Stub::create(['implements' => [FillerInterface::class]]);
        $filler2 = Stub::create(['implements' => [FillerInterface::class]]);

        $filler = new ChainedFiller([$filler1, $filler2]);
        expect($filler->getFillers())->toBe([$filler1, $filler2]);
    });

    it("should call the fill method of provided fillers in order", function () {
        $filler1 = Stub::create(['implements' => [FillerInterface::class]]);
        $filler2 = Stub::create(['implements' => [FillerInterface::class]]);

        $filler = new ChainedFiller([$filler1, $filler2]);
        $dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $filler);
        $builder = Stub::create(['extends' => Builder::class, 'params' => ['array', $dogmatist]]);

        expect($filler1)->toReceive('fill')->with($builder);
        expect($filler2)->toReceiveNext('fill')->with($builder);
        $filler->fill($builder);
    });
});
