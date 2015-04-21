<?php

use Bravesheep\Dogmatist\ReplacableLink;

describe("ReplacableLink", function () {
    it("should be constructed correctly", function () {
        $repl = new ReplacableLink([1, 2], ['test']);
        expect($repl->value)->toBe([1, 2]);
        expect($repl->fields)->toBe(['test']);
    });
});
