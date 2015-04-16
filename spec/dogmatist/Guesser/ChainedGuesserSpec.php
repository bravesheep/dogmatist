<?php

use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Guesser\ChainedGuesser;
use kahlan\plugin\Stub;

describe("ChainedGuesser", function () {
    it("should be constructed without any fillers by default", function () {
        $filler = new ChainedGuesser();
        $dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $filler);
        expect($dogmatist->getGuesser())->toBe($filler);
        expect($filler->getFillers())->toHaveLength(0);
    });

    it("should add all fillers provided in the constructor", function () {
        $filler1 = Stub::create(['implements' => ['Bravesheep\Dogmatist\Guesser\GuesserInterface']]);
        $filler2 = Stub::create(['implements' => ['Bravesheep\Dogmatist\Guesser\GuesserInterface']]);

        $filler = new ChainedGuesser([$filler1, $filler2]);
        expect($filler->getFillers())->toBe([$filler1, $filler2]);
    });

    it("should call the fill method of provided fillers in order", function () {
        $filler1 = Stub::create(['implements' => ['Bravesheep\Dogmatist\Guesser\GuesserInterface']]);
        $filler2 = Stub::create(['implements' => ['Bravesheep\Dogmatist\Guesser\GuesserInterface']]);

        $filler = new ChainedGuesser([$filler1, $filler2]);
        $dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $filler);
        $builder = Stub::create(['extends' => 'Bravesheep\Dogmatist\Builder', 'params' => ['array', $dogmatist]]);

        expect($filler1)->toReceive('fill')->with($builder);
        expect($filler2)->toReceiveNext('fill')->with($builder);
        $filler->fill($builder);
    });
});
