<?php

use Bravesheep\Dogmatist\Exception\InvalidArgumentException;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Dogmatist;

describe("Factory", function () {
    it("creates an instance of Dogmatist", function () {
        expect(Factory::create())->toBeAnInstanceOf(Dogmatist::class);
    });

    it("should fail for invalid values of faker", function () {
        $task = function () {
            Factory::create(22);
        };
        expect($task)->toThrow(new InvalidArgumentException());
    });
});
