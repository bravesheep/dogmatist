<?php

use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Guesser\ChainedGuesser;

describe("ChainedGuesser", function () {
    it("should be constructed without any fillers by default", function () {
        $filler = new ChainedGuesser();
        $dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $filler);
        expect($dogmatist->getGuesser())->toBe($filler);
        expect($filler->getFillers())->toHaveLength(0);
    });

    it("should add all fillers provided in the constructor", function () {
        $filler1 = Mockery::mock('Bravesheep\Dogmatist\Guesser\GuesserInterface');
        $filler2 = Mockery::mock('Bravesheep\Dogmatist\Guesser\GuesserInterface');

        $filler = new ChainedGuesser([$filler1, $filler2]);
        expect($filler->getFillers())->toBe([$filler1, $filler2]);
    });

    it("should call the fill method of provided fillers in order", function () {
        $filler1 = Mockery::mock('Bravesheep\Dogmatist\Guesser\GuesserInterface');
        $filler2 = Mockery::mock('Bravesheep\Dogmatist\Guesser\GuesserInterface');
        $filler = new ChainedGuesser([$filler1, $filler2]);

        $builder = Mockery::mock('Bravesheep\Dogmatist\Builder');

        $filler1->shouldReceive('fill')->with($builder)->once()->ordered();
        $filler2->shouldReceive('fill')->with($builder)->once()->ordered();

        $filler->fill($builder);
    });
});
