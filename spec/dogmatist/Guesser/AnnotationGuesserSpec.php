<?php

use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Guesser\AnnotationGuesser;

describe("AnnotationGuesser", function () {
    beforeEach(function () {
        $this->filler = new AnnotationGuesser();
        $this->dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $this->filler);
    });

    it("should be inserted in dogmatist", function () {
        expect($this->dogmatist->getGuesser())->toBe($this->filler);
    });

    it("should create the properties for the constructor", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\WithConstructorTest');

        expect($builder->getClass())->toBe('Bravesheep\Spec\Annotated\WithConstructorTest');
        expect($builder->hasConstructor())->toBe(true);

        $constructor = $builder->constructor();
        expect($constructor->getFields())->toHaveLength(2);
        expect($constructor->isPositional())->toBe(true);

        expect($constructor->get(0)->getType())->toBe(Field::TYPE_LINK);
        expect($constructor->get(1)->getType())->toBe(Field::TYPE_FAKE);

        expect($constructor->get(0)->getLinkTarget())->toBe('another');
        expect($constructor->get(1)->getFakedType())->toBe('randomNumber');
    });

    it("should set up a relation", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\RelationFieldTest');

        expect($builder->get('relation')->getType())->toBe(Field::TYPE_RELATION);
        expect($builder->get('relation')->getRelated()->hasConstructor())->toBe(false);
    });

    it("should set up a relation with a constructor", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\RelationFieldWithConstructorTest');

        expect($builder->get('relation')->getType())->toBe(Field::TYPE_RELATION);
        expect($builder->get('relation')->getRelated()->hasConstructor())->toBe(true);
    });

    it("should set up a none field", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\NoneFieldTest');

        expect($builder->get('none')->getType())->toBe(Field::TYPE_NONE);
    });

    it("should set up a select field", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\SelectFieldTest');

        expect($builder->get('select')->getType())->toBe(Field::TYPE_SELECT);
    });

    it("should set up a value field", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\ValueFieldTest');

        expect($builder->get('value')->getType())->toBe(Field::TYPE_VALUE);
    });

    it("should set up a fake field", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\FakedFieldTest');

        expect($builder->get('faked')->getType())->toBe(Field::TYPE_FAKE);
    });

    it("should use annotations if the class is not annotated", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\MissingClassAnnotationTest');

        expect($builder->getFields())->toHaveLength(1);
        expect($builder->get('faked')->getType())->toBe(Field::TYPE_FAKE);
        expect($builder->hasConstructor())->toBe(false);
    });

    it("should set up a field when annotated with a field annotation", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Annotated\FieldAnnotatedTest');

        expect($builder->get('field')->getType())->toBe(Field::TYPE_FAKE);
        expect($builder->get('field')->isMultiple())->toBe(true);
    });

    it("should set strict mode from the dogma annotation", function () {
        $nonstrict = $this->dogmatist->create('Bravesheep\Spec\Annotated\WithConstructorTest');
        $strict_implicit = $this->dogmatist->create('Bravesheep\Spec\Annotated\FakedFieldTest');
        $strict_explicit = $this->dogmatist->create('Bravesheep\Spec\Annotated\ValueFieldTest');

        expect($nonstrict->isStrict())->toBe(false);
        expect($strict_explicit->isStrict())->toBe(true);
        expect($strict_implicit->isStrict())->toBe(true);
    });
});
