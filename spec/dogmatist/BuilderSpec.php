<?php

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Exception\BuilderException;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Guesser\NoneGuesser;

describe("Builder", function () {
    before(function () {
        $this->dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, new NoneGuesser());
    });

    beforeEach(function () {
        $this->builder = new Builder('object', $this->dogmatist);
    });

    it("should have a field defined", function () {
        $this->builder->fake('example', 'number');
        expect($this->builder->has('example'))->toBe(true);
    });

    it("should create a faked field", function () {
        $this->builder->fake('example', 'number');
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_FAKE);
        expect($field->isType(Field::TYPE_FAKE))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getFakedOptions())->toBe([]);
        expect($field->getFakedType())->toBe('number');
    });

    it("should update a field to none", function () {
        $this->builder->fake('example', 'number');
        $this->builder->none('example');
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_NONE);
        expect($field->isType(Field::TYPE_NONE))->toBe(true);
    });

    it("should create a selection field", function () {
        $this->builder->select('example', [1, 2, 3, 4, 5]);
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_SELECT);
        expect($field->isType(Field::TYPE_SELECT))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getSelection())->toBe([1, 2, 3, 4, 5]);
    });

    it("should create a selection field for a single value", function () {
        $this->builder->value('example', 'value');
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_VALUE);
        expect($field->isType(Field::TYPE_VALUE))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getSelection())->toBe(['value']);
    });

    it("should create a relation to another builder", function () {
        $other = $this->builder->relation('example', 'array')->fake('field', 'string');
        $field = $this->builder->get('example');

        expect($other)->not->toBe($this->builder);
        expect($field->getType())->toBe(Field::TYPE_RELATION);
        expect($field->isType(Field::TYPE_RELATION))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getRelated())->toBe($other);
        expect($other->getClass())->toBe('array');
        expect($other->done())->toBe($this->builder);
    });

    it("should create a link to another builder", function () {
        $this->builder->link('example', 'another');
        $field = $this->builder->get('example');

        expect($field->getType())->toBe(Field::TYPE_LINK);
        expect($field->isType(Field::TYPE_LINK))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getLinkTarget())->toBe('another');
    });

    it("should create a callback field", function () {
        $cb = function () { return 0; };
        $this->builder->callback('example', $cb);
        $field = $this->builder->get('example');

        expect($field->getType())->toBe(Field::TYPE_CALLBACK);
        expect($field->isType(Field::TYPE_CALLBACK))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getCallback())->toBe($cb);
    });

    it("should return the dogmatist instance when done", function () {
        expect($this->builder->done())->toBe($this->dogmatist);
    });

    it("should set a field to produce multiple items", function () {
        $this->builder->fake('example', 'number');
        $this->builder->multiple('example', 2, 5);
        $field = $this->builder->get('example');

        expect($field->isMultiple())->toBe(true);
        expect($field->isSingular())->toBe(false);
        expect($field->getMin())->toBe(2);
        expect($field->getMax())->toBe(5);
    });

    it("should set a field to produce a single item", function () {
        $this->builder->fake('example', 'number');
        $this->builder->multiple('example', 2, 5);
        $this->builder->single('example');
        $field = $this->builder->get('example');

        expect($field->isMultiple())->toBe(false);
        expect($field->isSingular())->toBe(true);
    });

    it("should set a field to produce a single item as an array if set via multiple", function () {
        $this->builder->fake('example', 'number');
        $this->builder->multiple('example', 1, 1);
        $field = $this->builder->get('example');

        expect($field->isMultiple())->toBe(true);
        expect($field->isSingular())->toBe(false);
    });

    it("should not be possible to add a field to a builder that is not keyed using int or string", function () {
        $task = function () {
            $this->builder->fake(5.3, 'number');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should also set strictness to created children and constructors", function () {
        $this->builder->relation('example', 'array')->fake('example', 'string');
        $this->builder->constructor()->argValue(10);
        $this->builder->setStrict(false);
        expect($this->builder->constructor()->isStrict())->toBe(false);
        expect($this->builder->get('example')->getRelated()->isStrict())->toBe(false);
    });

    it("should not have a field that was never defined", function () {
        expect($this->builder->has('field_not_used'))->toBe(false);
    });

    it("should create a unique field", function () {
        $this->builder->fake('example', 'number');
        $this->builder->unique('example');
        $field = $this->builder->get('example');

        expect($field->isUnique())->toBe(true);
    });

    it("should remove uniqueness from a field", function () {
        $this->builder->fake('example', 'number');
        $this->builder->unique('example');
        $this->builder->unique('example', false);
        $field = $this->builder->get('example');

        expect($field->isUnique())->toBe(false);
    });
});
