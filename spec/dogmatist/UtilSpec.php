<?php

use Bravesheep\Dogmatist\Dogmatist;
use Bravesheep\Dogmatist\Util;

describe("Util", function () {
    describe("::normalizeName", function () {
        it("normalizes camelcased to underscored", function () {
            expect(Util::normalizeName("someCamelCasedExample"))->toBe('some_camel_cased_example');
            expect(Util::normalizeName("FirstLetterCamelCased"))->toBe('first_letter_camel_cased');
            expect(Util::normalizeName("Mixed_styleCamelCase"))->toBe('mixed_style_camel_case');
        });

        it("does not change underscored names", function () {
            expect(Util::normalizeName("test"))->toBe("test");
            expect(Util::normalizeName("test_case"))->toBe("test_case");
        });
    });

    describe("::isSystemClass", function () {
        it("checks for the array and object system classes", function () {
            expect(Util::isSystemClass('array'))->toBe(true);
            expect(Util::isSystemClass('object'))->toBe(true);
            expect(Util::isSystemClass('stdClass'))->toBe(true);
        });

        it("does not return true for user classes", function () {
            expect(Util::isSystemClass(Dogmatist::class))->toBe(false);
        });
    });

    describe("::isUserClass", function () {
        it("checks whether an identifier is not a system class", function () {
            expect(Util::isUserClass(Dogmatist::class))->toBe(true);
        });

        it("returns false for objects and arrays", function () {
            expect(Util::isUserClass('array'))->toBe(false);
            expect(Util::isUserClass('object'))->toBe(false);
            expect(Util::isUserClass('stdClass'))->toBe(false);
        });
    });
});
