<?php

use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Guesser\TypeResolver;

describe("TypeResolver", function () {
    it("should register a new mapping", function () {
        TypeResolver::registerMapping('abc', Field::TYPE_FAKE, []);
        expect(TypeResolver::resolve('abc'))->toBe([Field::TYPE_FAKE, false, []]);
    });

    it("should register multiple new mappings", function () {
        TypeResolver::registerMappings([
            'xyz' => [Field::TYPE_FAKE, false, []],
            'wyz' => [Field::TYPE_VALUE, false, ['test']],
        ]);

        expect(TypeResolver::resolve('xyz'))->toBe([Field::TYPE_FAKE, false, []]);
        expect(TypeResolver::resolve('wyz'))->toBe([Field::TYPE_VALUE, false, ['test']]);
    });

    it("should not resolve an unknown type", function () {
        expect(TypeResolver::resolve('some_nonexistant_type'))->toBe(false);
    });

    it("should resolve types starting with is or has to be booleans", function () {
        expect(TypeResolver::resolve('isTrue'))->toBe([Field::TYPE_FAKE, false, ['boolean']]);
        expect(TypeResolver::resolve('has_value'))->toBe([Field::TYPE_FAKE, false, ['boolean']]);
    });

    it("should resolve types ending in at to be datetimes", function () {
        expect(TypeResolver::resolve("updatedAt"))->toBe([Field::TYPE_FAKE, false, ['dateTime']]);
        expect(TypeResolver::resolve("created_at"))->toBe([Field::TYPE_FAKE, false, ['dateTime']]);
        expect(TypeResolver::resolve("updatedat"))->toBe(false);
    });

    it("should resolve different styles of first name to the same type", function () {
        expect(TypeResolver::resolve('firstname'))->toBe([Field::TYPE_FAKE, false, ['firstName']]);
        expect(TypeResolver::resolve('first_name'))->toBe([Field::TYPE_FAKE, false, ['firstName']]);
        expect(TypeResolver::resolve('firstName'))->toBe([Field::TYPE_FAKE, false, ['firstName']]);
    });
});
