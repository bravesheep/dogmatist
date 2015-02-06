<?php

use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Dogmatist;

describe("Factory", function () {
    it("creates an instance of Dogmatist", function () {
        expect(Factory::create())->toBeAnInstanceOf(Dogmatist::class);
    });
});
