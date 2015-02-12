<?php

use Bravesheep\Dogmatist\Exception\SampleException;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Spec\Example;


describe("Sampler", function () {
    beforeEach(function () {
        $this->dogmatist = Factory::create();
        $this->sampler = $this->dogmatist->getSampler();
    });

    it("should pick one from a selection", function () {
        $builder = $this->dogmatist->create('object')->select('example', [1, 2, 3, 4, 5]);
        $sample = $this->sampler->sample($builder);
        expect($sample->example)->toMatch(function ($actual) {
            return in_array($actual, [1, 2, 3, 4, 5]);
        });
    });

    it("should inject the predefined selection", function () {
        $builder = $this->dogmatist->create('array')->value('test', 42);
        $sample = $this->sampler->sample($builder);
        expect($sample['test'])->toBe(42);
    });

    it("should fake values", function () {
        $builder = $this->dogmatist->create('object')->fake('number', 'randomNumber')->fake('word', 'word');
        $sample = $this->sampler->sample($builder);
        expect($sample->number)->toBeA('integer');
        expect($sample->word)->toBeA('string');
    });

    it("should generate child objects", function () {
        $builder = $this->dogmatist->create('object')
            ->relation('sub', 'array')
                ->value('test', 10)
            ->done()
        ;
        $sample = $this->sampler->sample($builder);
        expect($sample->sub['test'])->toBe(10);
    });

    it("should create an array if multiple are specified for a field", function () {
        $builder = $this->dogmatist->create('object')->fake('number', 'randomNumber')->multiple('number', 2, 5);
        $sample = $this->sampler->sample($builder);
        expect($sample->number)->toBeA('array');
    });

    it("should generate multiple samples", function () {
        $builder = $this->dogmatist->create('object')->fake('number', 'randomNumber');
        $samples = $this->sampler->samples($builder, 10);
        expect($samples)->toHaveLength(10);
    });

    it("should fail when trying to sample using a non-existant type in faker", function () {
        $task = function() {
            $builder = $this->dogmatist->create('object')->fake('number', 'a_nonexistant_generator');
            $this->sampler->sample($builder);
        };
        expect($task)->toThrow(new SampleException());
    });

    it("should fail when a field is set to a non-existant type", function () {
        $task = function() {
            $builder = $this->dogmatist->create('object');
            $field = $builder->get('number');
            $prop = new ReflectionProperty(Field::class, 'type');
            $prop->setAccessible(true);
            $prop->setValue($field, PHP_INT_MAX);
            $this->sampler->sample($builder);
        };
        expect($task)->toThrow(new SampleException());
    });

    describe("working with multiple builders", function () {
        beforeEach(function () {
            $this->builder1 = $this->dogmatist->create('object')->fake('number', 'randomNumber')->save('numbered', 1);
            $this->builder2 = $this->dogmatist->create('object')->link('number', 'numbered')->save('linked', 2);
        });

        it("should link a sample from another builder", function () {
            $sample = $this->sampler->sample($this->builder2);
            expect($sample->number->number)->toBeA('integer');
        });

        it("should retrieve multiple objects from a multiple relation", function () {
            $this->builder2->get('number')->setMultiple(2, 5);
            $sample = $this->sampler->sample($this->builder2);
            expect($sample->number)->toBeA('array');
        });
    });

    describe("working with classes", function () {
        it("should properly assign a public property", function () {
            $builder = $this->dogmatist->create(Example::class)->fake('pub', 'randomNumber');
            $sample = $this->sampler->sample($builder);
            expect($sample->pub)->toBeA('integer');
        });

        it("should be able to assign to a private property with setter access", function () {
            $builder = $this->dogmatist->create(Example::class)->fake('priv_pub', 'randomNumber');
            $sample = $this->sampler->sample($builder);
            expect($sample->getPrivPub())->toBeA('integer');
        });

        describe("in strict mode", function () {
            it("should not be able to assign to a non-accessible property", function () {
                $task = function () {
                    $builder = $this->dogmatist->create(Example::class)->fake('priv_priv', 'randomNumber');
                    $this->sampler->sample($builder);
                };
                expect($task)->toThrow(new SampleException());
            });

            it("should not be able to assign to a non-existing property", function () {
                $task = function () {
                    $builder = $this->dogmatist->create(Example::class)->fake('fake', 'randomNumber');
                    $this->sampler->sample($builder);
                };
                expect($task)->toThrow(new SampleException());
            });
        });

        describe("in non-strict mode", function () {
            it("should assign to a non-accessible property", function () {
                $builder = $this->dogmatist->create(Example::class)
                    ->fake('priv_priv', 'randomNumber')
                    ->setStrict(false);
                $sample = $this->sampler->sample($builder);
                expect($sample->getPrivPriv())->toBeA('integer');
            });

            it("should assign to a non-existing property", function () {
                $builder = $this->dogmatist->create(Example::class)
                    ->fake('fake', 'randomNumber')
                    ->setStrict(false);
                $sample = $this->sampler->sample($builder);
                expect($sample->fake)->toBeA('integer');
            });
        });
    });

    it("should call listeners for a builder", function () {
        $builder = $this->dogmatist->create('array')->fake('num', 'randomNumber');
        $received = null;
        $builder->onCreate(function ($rec) use (&$received) {
            $received = $rec;
        });
        $sample = $this->sampler->sample($builder);
        expect($received)->toBe($sample);

    });
});
