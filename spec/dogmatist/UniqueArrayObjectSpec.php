<?php

use Bravesheep\Dogmatist\UniqueArrayObject;

describe("UniqueArrayObject", function () {
    it("should generate new identifiers", function () {
        $a = new UniqueArrayObject();
        $b = new UniqueArrayObject();

        expect($b->getId())->toBe($a->getId() + 1);
    });
});
