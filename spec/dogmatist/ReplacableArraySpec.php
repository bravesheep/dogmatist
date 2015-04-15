<?php

use Bravesheep\Dogmatist\ReplacableArray;
use Bravesheep\Dogmatist\ReplacableLink;

describe("ReplacableArray", function () {
    it("should be constructed correctly", function () {
        $link = new ReplacableLink([1, 2], 'test');
        $arr = new ReplacableArray([$link]);

        expect($arr->data)->toBe([$link]);
    });
});
