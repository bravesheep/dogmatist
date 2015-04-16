<?php

use Bravesheep\Dogmatist\Exception\InvalidArgumentException;
use Bravesheep\Dogmatist\Factory;

describe("Factory", function () {
    it("creates an instance of Dogmatist", function () {
        expect(Factory::create())->toBeAnInstanceOf('Bravesheep\Dogmatist\Dogmatist');
    });

    it("should fail for invalid values of faker", function () {
        $task = function () {
            Factory::create(22);
        };
        expect($task)->toThrow(new InvalidArgumentException());
    });
});
