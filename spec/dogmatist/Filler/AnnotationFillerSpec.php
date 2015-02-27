<?php

use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Filler\AnnotationFiller;
use Bravesheep\Spec\Annotated\FakedFieldTest;
use Bravesheep\Spec\Annotated\FieldAnnotatedTest;
use Bravesheep\Spec\Annotated\MissingClassAnnotationTest;
use Bravesheep\Spec\Annotated\NoneFieldTest;
use Bravesheep\Spec\Annotated\RelationFieldTest;
use Bravesheep\Spec\Annotated\RelationFieldWithConstructorTest;
use Bravesheep\Spec\Annotated\SelectFieldTest;
use Bravesheep\Spec\Annotated\ValueFieldTest;
use Bravesheep\Spec\Annotated\WithConstructorTest;
use kahlan\plugin\Stub;


describe("AnnotationFiller", function () {
    beforeEach(function () {
        $this->filler = new AnnotationFiller();
        $this->dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $this->filler);
    });

    it("should be inserted in dogmatist", function () {
        expect($this->dogmatist->getFiller())->toBe($this->filler);
    });

    it("should create the properties for the constructor", function () {
        $builder = $this->dogmatist->create(WithConstructorTest::class);

        expect($builder->getClass())->toBe(WithConstructorTest::class);
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
        $builder = $this->dogmatist->create(RelationFieldTest::class);

        expect($builder->get('relation')->getType())->toBe(Field::TYPE_RELATION);
        expect($builder->get('relation')->getRelated()->hasConstructor())->toBe(false);
    });

    it("should set up a relation with a constructor", function () {
        $builder = $this->dogmatist->create(RelationFieldWithConstructorTest::class);

        expect($builder->get('relation')->getType())->toBe(Field::TYPE_RELATION);
        expect($builder->get('relation')->getRelated()->hasConstructor())->toBe(true);
    });

    it("should set up a none field", function () {
        $builder = $this->dogmatist->create(NoneFieldTest::class);

        expect($builder->get('none')->getType())->toBe(Field::TYPE_NONE);
    });

    it("should set up a select field", function () {
        $builder = $this->dogmatist->create(SelectFieldTest::class);

        expect($builder->get('select')->getType())->toBe(Field::TYPE_SELECT);
    });

    it("should set up a value field", function () {
        $builder = $this->dogmatist->create(ValueFieldTest::class);

        expect($builder->get('value')->getType())->toBe(Field::TYPE_VALUE);
    });

    it("should set up a fake field", function () {
        $builder = $this->dogmatist->create(FakedFieldTest::class);

        expect($builder->get('faked')->getType())->toBe(Field::TYPE_FAKE);
    });

    it("should use annotations if the class is not annotated", function () {
        $builder = $this->dogmatist->create(MissingClassAnnotationTest::class);

        expect($builder->getFields())->toHaveLength(1);
        expect($builder->get('faked')->getType())->toBe(Field::TYPE_FAKE);
        expect($builder->hasConstructor())->toBe(false);
    });

    it("should set up a field when annotated with a field annotation", function () {
        $builder = $this->dogmatist->create(FieldAnnotatedTest::class);

        expect($builder->get('field')->getType())->toBe(Field::TYPE_FAKE);
        expect($builder->get('field')->isMultiple())->toBe(true);
    });

    it("should set strict mode from the dogma annotation", function () {
        $nonstrict = $this->dogmatist->create(WithConstructorTest::class);
        $strict_implicit = $this->dogmatist->create(FakedFieldTest::class);
        $strict_explicit = $this->dogmatist->create(ValueFieldTest::class);

        expect($nonstrict->isStrict())->toBe(false);
        expect($strict_explicit->isStrict())->toBe(true);
        expect($strict_implicit->isStrict())->toBe(true);
    });
});
