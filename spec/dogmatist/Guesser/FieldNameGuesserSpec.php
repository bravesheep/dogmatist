<?php

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Guesser\FieldNameGuesser;
use Bravesheep\Dogmatist\Guesser\TypeResolver;

describe("FieldNameGuesser", function () {
    beforeEach(function () {
        $this->guesser = new FieldNameGuesser();
        $this->dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $this->guesser);
    });

    it("should correctly identify the fields", function () {
        $cb = function () { return 0; };

        TypeResolver::registerMapping('addresses', Field::TYPE_FAKE, ['streetAddress'], [2, 10]);
        TypeResolver::registerMappings([
            'link_test' => [Field::TYPE_LINK, false, ['test']],
            'relation_test' => [Field::TYPE_RELATION, false, ['object']],
            'value_test' => [Field::TYPE_VALUE, false, ['value']],
            'callback_test' => [Field::TYPE_CALLBACK, false, [$cb]],
            'none_test' => [Field::TYPE_NONE, false, []],
            'select_test' => [Field::TYPE_SELECT, false, [['a', 'b']]],
        ]);

        /** @var Builder $builder */
        $builder = $this->dogmatist->create('Bravesheep\Spec\Named\NamedTest');

        expect($builder->getFields())->toHaveLength(9);
        expect($builder->get('addresses')->getFakedType())->toBe('streetAddress');
        expect($builder->get('firstName')->getFakedType())->toBe('firstName');
        expect($builder->get('last_name')->getFakedType())->toBe('lastName');

        expect($builder->get('linkTest')->isType(Field::TYPE_LINK))->toBe(true);
        expect($builder->get('relationTest')->isType(Field::TYPE_RELATION))->toBe(true);
        expect($builder->get('valueTest')->isType(Field::TYPE_VALUE))->toBe(true);
        expect($builder->get('callbackTest')->isType(Field::TYPE_CALLBACK))->toBe(true);
        expect($builder->get('noneTest')->isType(Field::TYPE_NONE))->toBe(true);
        expect($builder->get('selectTest')->isType(Field::TYPE_SELECT))->toBe(true);
    });

    it("should not map an unknown field type", function () {
        /** @var Builder $builder */
        $builder = $this->dogmatist->create('Bravesheep\Spec\Named\NonExistantTypeTest');

        expect($builder->getFields())->toHaveLength(0);
        expect($builder->has('propertyWithoutType'))->toBe(false);
    });

    it("should not map a non-accessible field", function () {
        /** @var Builder $builder */
        $builder = $this->dogmatist->create('Bravesheep\Spec\Named\InaccessibleTest');

        expect($builder->getFields())->toHaveLength(0);
        expect($builder->has('firstname'))->toBe(false);
    });
});
